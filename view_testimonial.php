
<?php
try{
  //connecting to database and finding the maximum number of items
  $con = new PDO("mysql:host=localhost;dbname=s_database","root","");
  $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  $stmt = $con->prepare("select count(id) as count_id where approved = 1");
  $stmt->execute();
  $result = $stmt->fetchAll();
  while($result as $row)
  {
    $id_count = $row['count_id'];
  }
  $count  = $id_count;
  $con = null;
  
  //Setting the current page number
  $pagenumber = 1;//if no page number
  if(isset($_GET['id']))
  {
    $pagenumber = preg_replace('#[^0-9]#','',$_GET['pn']);
  }
  
  //number of rows
  $pagerows = 10;
  
  //finding the last page and safeguarding it
  $last = ceil($count / $pagerows);
    if($last < 1)
  {
    $last = 1;
  }
 
  //safe guarding page number
  if($pagenumber < 1)
  {
    $pagenumber = 1;
  }
  else if($pagenumber > $last)
  {
    $pagenumber = $last;
  }
  // setting the limit for sql query
   $limit = 'Limit'.($pagenumber-1) * $pagerows.','.$pagerows;
  
  $textfortest = "<strong>We have".$count."Testimonials</strong><br>Page no".$pagenumber."of".$last;
  
  /*Now setting the pagecontrolls */
 //The controlls previous to the current page
  if($last != 1)
     {
        $pagecontroll = '';
        if($pagenumber > 1)
        {
          $pagecontroll .= '<a href="'.$_SERVER["PHP_SELF"].'?pn='.$pagenumber-1.'">Prev</a>';
          for($i = $pagenumber-4;$i<$last;$i++)
          {
            if($i > 1)
            {
              $pagecontroll .= '<a href="'.$_SERVER[PHP_SELF].'?pn='.$i.'">'.$i.'</a>';
            }
          }
        }
        //centerpage
        $pagecontroll .= ' '.$pagenumber.' &nbsp';
        //The pages next to the current page
        for($i=$pagenumber+1;$i<$last;$i++)
        {
          $pagecontroll .= '<a href="'.$_SERVER[PHP_SELF].'?pn='.$i.'">'.$i.'</a>';
          if($i >= $pagenumber + 4)
          {
            break;
          }
        }
        if($pagenumber != $last)
        {
          $pagecontroll .= '<a href="'.$_SERVER["PHP_SELF"].'?pn='.$pagenumber+1.'">Prev</a>';
        }
  
     }
  
  //fetching all the testimonials
  $con = new PDO("mysql:host=localhost;dbname=s_database","root","");
  $stmt = $con->prepare("select id,firstname,lastname,testimonial,datemade from testimonials where approved = 1 order by id desc :limit");
    $stmt->bindParam(":limit",$limit);
  $stmt->execute();
  $result_test = $stmt->fetchAll();
  $list = '';
  foreach ($result_test as $row_test){
    $firstname = $row_test['firstname'];
    $lastname = $row_test['lastname'];
    $testimonial = $row_test['testimonial'];
    $update = $row_test['datemade'];
    $datemade = strftime('%b %d,%Y',strtotime($update));
    $list .= "<p>By ".$firstname.' '.$lastname.' on '.$datemade.'</p>';    
  }
  $con = null;
  
}
catch(Exception $ex){
 echo $ex->getMessage();
}

?>
<html>
  <head>
  </head>
  <body>
    <?php echo   $textfortest ;?><br>
    <?php echo $list; ?><br>
    <?php echo $pagecontroll; ?><br>
  </body>
</html>
