<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
    <div class="container">
        <div id="header">
            <h1>Foodblog</h1>
            <a href="new_post.php"><button>Nieuwe post</button></a>
        </div>
        <h3>Populaire chefs</h3>
    <?php
        include 'connection.php';
        // Execute the SQL query
        $sql = "SELECT SUM(posts.likes) AS likes, auteur.auteur
        FROM posts
        JOIN auteur ON posts.auteur_id = auteur.auteur_id
        GROUP BY posts.auteur_id
        HAVING SUM(posts.likes) >= 10
        ORDER BY likes DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    if ($stmt->rowCount() > 0) {
        ?>
        <ul>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?><li><?php echo $row['auteur'] ?></li>
            <?php } ?>
            </ul>
    <?php } 
        // Execute the SQL query
        $sql = "SELECT posts.*, auteur.auteur
        FROM posts
        JOIN auteur ON posts.auteur_id = auteur.auteur_id
        ORDER BY posts.likes DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

    // Display the result on the web page
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <div class="post">
                    <div class="header">
                        <h2><?php echo $row['titel']?></h2> 
                        <img src=<?php echo $row['img_url']?>/>
                    </div>  
                    <span class="details"><?php echo 'Geschreven op: ' . $row['datum'] . ' door'?><b> <?php echo $row['auteur']?></b></span>
                    <span class="right">
                        <form action="index.php" method="post">
                            <button type="submit" value="<?php echo $row['id']; ?>" name="like">
                                <?php echo $row['likes']; ?> likes
                            </button>
                        </form>
                    </span>
                    <span class="details">
                        Tags: 
                    <?php
                        $sql_tag = "SELECT * FROM tags
                        JOIN posts_tags USING (tag_id)
                        WHERE post_id = '" . $row['id'] . "'";
                        $stmt_tag = $conn->prepare($sql_tag);
                        $stmt_tag->execute();
                    if ($stmt_tag->rowCount() > 0) {
                        while ($row_tag = $stmt_tag->fetch(PDO::FETCH_ASSOC)) {
                            ?> <a href="lookup.php?tag=<?= $row_tag['titel']?>"><?php echo $row_tag['titel'] . ', ';?></a><?php
                        }
                    }
                    ?>  
                    </span> 
                    <p><?php echo $row['inhoud']?></p>
                </div>
                <?php
        }
        if (isset($_POST['like'])) {
            $id = $_POST['like'];
            $sql = "UPDATE posts SET likes = likes + 1 WHERE id = $id;";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $id = 0;
        }
    } else {
        echo "0 results";
    }
    ?>
    </div>
    </body>
</html>