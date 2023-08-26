<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class VerificationController extends BaseController
{
    public function __construct()
    {
        //
    }

    private function addVerificationResultsRecord(string $user_id, string $file_type = "JSON", $verification_result):bool {

        DB::table('verification_results')->insert([
            'user_id' => $user_id,
            'file_type' => $file_type,
            'verification_result' => 
                (is_array($verification_result)?json_encode($verification_result):$verification_result)
        ]);
        return true;
    }

    private function generateHash(string $key_path, $value): string {
        return hash("sha256",json_encode([$key_path => (
            isset($value)?
            $value:""
        )]));
    }
    public function getVerification(Request $request)
    {

        $payload_data = $request->get("data"); // payload as multi-dimensional arrays
        $result_data_id = (isset($payload_data['id'])?$payload_data['id']:0); 

        if (isset($payload_data['issuer']['name'])){
            $result_data_issuer=$payload_data['issuer']['name'];
        } else {
            $result_data_issuer="unset";
        }

        $error_array = [];
        if (isset($payload_data['recipient']['name']) && isset($payload_data['recipient']['email'])){
            $condition_1_pass  = true;
        } else {
            $condition_1_pass  = false;
            $error_array[] = "invalid_recipient";
        }

        if(isset($payload_data['issuer']['name']) && isset($payload_data['issuer']['identityProof'])){
            $condition_2_pass = true;
        } else {
            $condition_2_pass = false;
            $error_array[] = "invalid_issuer";
        }
        //check the issuer.identityProof.key DNS TXT record against issuer.identityProof.location (domain name)        
        if ($condition_2_pass){
            //check the issuer.identityProof.key DNS TXT record against issuer.identityProof.location (domain name)
            $issuer_identityProof_location = (isset($payload_data['issuer']['identityProof']['location'])?
                    $payload_data['issuer']['identityProof']['location']:
                    "");
            $issuer_identityProof_key = (isset($payload_data['issuer']['identityProof']['key'])?
                    $payload_data['issuer']['identityProof']['key']:
                    "");
            $dns_lookup_endpoint="https://dns.google/resolve?name={$issuer_identityProof_location}&type=TXT";
            $guzzle_client = new \GuzzleHttp\Client();
            $guzzle_response =  $guzzle_client->post($dns_lookup_endpoint);

            $guzzle_statusCode = $guzzle_response->getStatusCode();

            if($guzzle_statusCode == 200){
                // got a successfullookup... need  to loopthrough to find the key...
                $guzzle_content = json_decode($guzzle_response->getBody(),true);
                $answer_found = false;
                if (isset($guzzle_content['Answer'])){
                    foreach($guzzle_content['Answer'] as $answer){
                        if ( str_contains($answer['data'],$issuer_identityProof_key ) ){
                            $answer_found = true;
                            break;
                        }
                    }
                }
                if  (!$answer_found){
                    $condition_2_pass = false;
                    $error_array[] = "invalid_issuer";
                }
            } else {
                $condition_2_pass = false;
                $error_array[] = "invalid_issuer";
            }

        }// otherwise - no pointin checking the dns TXT records if the above address and key is missing from the payload

        
        $payload_signature = $request->get("signature");
        if (isset($payload_signature["targetHash"])){
            $targetHash = $payload_signature["targetHash"];

            // computing targethash
            $hash_arry = [];
            
            //specify the path-naming of each key,and the correcponding value of each hash item 
            //if you with to add to the list of hashes,add it to the list below:
            $hash_array[] = $this->generateHash("id",$payload_data["id"]);
            $hash_array[] = $this->generateHash("name",$payload_data["name"]);
            $hash_array[] = $this->generateHash("recipient.name",$payload_data["recipient"]["name"]);
            $hash_array[] = $this->generateHash("recipient.email",$payload_data["recipient"]["email"]);
            $hash_array[] = $this->generateHash("issuer.name",$payload_data["issuer"]["name"]);
            $hash_array[] = $this->generateHash("issuer.identityProof.type",(isset($payload_data["issuer"]["identityProof"]["type"])?$payload_data["issuer"]["identityProof"]["type"]:""));
            $hash_array[] = $this->generateHash("issuer.identityProof.key",(isset($payload_data["issuer"]["identityProof"]["key"])?$payload_data["issuer"]["identityProof"]["key"]:""));
            $hash_array[] = $this->generateHash("issuer.identityProof.location",(isset($payload_data["issuer"]["identityProof"]["location"])?$payload_data["issuer"]["identityProof"]["location"]:""));
            $hash_array[] = $this->generateHash("issued",$payload_data["issued"]);
            sort($hash_array);
            $computed_target_hash=hash("sha256",json_encode($hash_array));

            //now to compare the  provided hash with the computed one
            if  ($targetHash === $computed_target_hash){
                $condition_3_pass = true;
            } else {
                $condition_3_pass = false;
                $error_array[] = "unverified";
            }
        } else {
            $condition_3_pass = false;
            $error_array[] = "unverified";
        }

        $verification_result = ($condition_1_pass && $condition_2_pass && $condition_3_pass?"verified":
            [
                "errors" => $error_array
            ]
        );
        $this->addVerificationResultsRecord($result_data_id, "JSON", $verification_result);

        return response()->json([
            "data" => [
                "issuer" => $result_data_issuer,
                "result" => $verification_result/*,
                "computed_target_hash" => $computed_target_hash,
                "targetHash" => $targetHash*/
                
            ]
        ]);


        /*
        $products = shopify()->getAllProducts(); //["ids" => "6632857895096"]);
        $dolibarr_product = dolibarr()->getAllProducts(); //getProduct(3469);
        return response()->json([
            "success" => true,
            "products" => $products,
            "dolibarr_product" => $dolibarr_product
        ]);
        */
    }
}
