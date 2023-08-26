Refactoring or improvements that I  would do, given more time:

- refactor the generateHash() function, to automate a process to loop through  all nested values, and generate the hash, via just one function call

- refactor the $condition_1_pass, $condition_2_pass, $condition_3_pass validation variables - to become more of an individual validation function call. 
this would  make  the validation  steps a  lot clearer, and less guesswork involved in interpreting the uses of those variables mentioned above