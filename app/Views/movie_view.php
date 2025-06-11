<!DOCTYPE html>
<html>
<head>
    <title>MongoDB Manipulation in CodeIgniter 4</title>
</head>
<body>
    <h1>MongoDB Manipulation:</h1>
   
    <?php 
    //echo $mvs;
    
    foreach($mvs as $m)
    {       
        echo 'Title: '.$m->title. ' ('.$m->year.')' . 
        ' made in '.$m->countries[0]; //. ' -- '. 'Rating: '. $m->imdb->rating->{'$numberDouble'}.'<br>';
        //var_dump($m->title);
        
        echo '<hr>';        
    }
    
    ?>       
 
</body>
</html>