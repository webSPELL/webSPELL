<?php
$_language->read_module('search');
if(isset($_GET['tag'])){
        $tag = $_GET['tag'];
        $sql = safe_query("SELECT * FROM ".PREFIX."tags WHERE tag='".$tag."'");
        if($sql->num_rows){
                $data = array();
                while($ds = mysqli_fetch_assoc($sql)){
                        if($ds['rel'] == "news"){
                                $data_check = Tags::getNews($ds['ID']);
                                if(is_array($data_check)){
                                        $data[] = $data_check;
                                }
                        }
                        elseif($ds['rel'] == "articles"){
                               $data_check = Tags::getArticle($ds['ID']);
                               if(is_array($data_check)){
                                        $data[] = $data_check;
                                }
                        }
                        elseif($ds['rel'] == "static"){
                               $data_check = Tags::getStaticPage($ds['ID']);
                               if(is_array($data_check)){
                                        $data[] = $data_check;
                                }
                        }
                }
                echo "<h1>".$_language->module['search']."</h1>";
                usort($data,array('Tags','sortByDate'));
                echo "<center><b>".count($data)."</b> ".$_language->module['results_found']."</center><br /><br />";
                foreach($data as $entry){
                        
                        $date = date("d.m.Y", $entry['date']);
                        $type = $entry['type'];
                        $auszug= $entry['content'];
                        $link = $entry['link'];
                        $title = $entry['title'];
                        eval ("\$search_tags = \"".gettemplate("search_tags")."\";");
                        echo $search_tags;
                        
                }
        }
        else{
                ?>
<div class="post">
        <h2 class="title">Tag suche</h2>
        <div class="entry">
                <p>
                        Keine Einträge zu "<?php echo htmlspecialchars($tag); ?>" gefunden
                </p>
        </div>
</div>
                        <?php
        }
}
else{
        function tags_top_10($a1,$a2){
                if($a1['count'] == $a2['count']) return 0;
                else return $a1['count']< $a2['count']? -1 : 1;
        }
        $tags = Tags::getTagCloud();
        usort($tags['tags'],"tags_top_10");
        $str = '';
        for($i=0;$i<min(10,count($tags['tags']));$i++){
                $tag = $tags['tags'][$i];
                $size = Tags::GetTagSizeLogarithmic($tag['count'], $tags['min'], $tags['max'], 10, 25, 0);
                $str .= " <a href='index.php?site=tags&amp;tag=".$tag['name']."' style='font-size:".$size."px;text-decoration:none;'>".$tag['name']."</a> ";
        }
        ?>
<div class="post">
        <h2 class="title">Top Tags</h2>
        <div class="entry">
                <p>
                <?php echo $str; ?>
                </p>
        </div>
</div>
<?php
}
?>