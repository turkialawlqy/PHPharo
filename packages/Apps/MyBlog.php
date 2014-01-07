<?php

/**
 * Apps_MyBlog
 * Simple Backend Blog System .
 * @package Apps
 * @author Mohammed Alashaal <fb.com/alash3al>
 * @copyright 2014
 * @version 1.0
 * @access public
 */
class Apps_MyBlog extends Database_MysqlDriver
{
    /**
     * Apps_MyBlog::__construct()
     * start Apps_MyBlog system .
     * @param string $dbhost
     * @param string $dbname
     * @param string $dbuser
     * @param string $dbpass
     * @return object
     */
    function __construct($dbhost, $dbname, $dbuser, $dbpass)
    {
        parent::__construct($dbhost, $dbname, $dbuser, $dbpass);
        $this->tables();
    }
    
    /**
     * Apps_MyBlog::newPost()
     * Create new Blog Post .
     * @param string $title
     * @param string $content
     * @param string $author
     * @param integer $category_id
     * @param mixed $time
     * @return bool
     */
    function newPost($title, $content, $author, $category_id, $time)
    {
        return (bool)$this->insert('blog_posts', 'post_title,post_content,post_author,post_cat_id,post_time',
        array($title, $content, $author, $category_id, $time));
    }
    
    /**
     * Apps_MyBlog::deletePost()
     * Delete a blog post(s) .
     * @param integer $post_id
     * @return bool
     */
    function deletePost($post_id)
    {
        return (bool)$this->delete('blog_posts', 'where post_id in ('.implode(', ', array_fill(1, count((array)$id), '?')).')', (array)$id);
    }
    
    /**
     * Apps_MyBlog::editPost()
     * Edit a blog post .
     * @param integer $post_id
     * @param string $new_title
     * @param string $new_content
     * @param string $new_author
     * @param integer $new_category_id
     * @param mixed $new_time
     * @return bool
     */
    function editPost($post_id, $new_title, $new_content, $new_author, $new_category_id, $new_time)
    {
        return (bool)$this->update('blog_posts', array(
                        'post_title' => $new_title,
                        'post_content' => $new_content,
                        'post_author' => $new_author,
                        'post_cat_id' => $new_category_id,
                        'post_time' => $new_time
        ), 'where post_id = ?', $post_id);
    }
    
    /**
     * Apps_MyBlog::getPostByID()
     * Get a post by it's id .
     * @param integer $post_id
     * @return object | null
     */
    function getPostByID($post_id)
    {
        $this->select('blog_posts', '*', 'where post_id = ?', $post_id);
        return $this->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Apps_MyBlog::searchPosts()
     * Full Text Search
     * @param string $keywords .
     * @return bool
     */
    function searchPosts($keywords)
    {
        return (bool)$this->query('
                SELECT *,
                       MATCH (post_title, post_content) AGAINST (?  IN BOOLEAN MODE) AS relevance
                FROM blog_posts
                WHERE MATCH (post_title, post_content) AGAINST (?  IN BOOLEAN MODE)
                ORDER BY relevance DESC
            ', array($keywords, $keywords));
    }
    
    /**
     * Apps_MyBlog::getPostsByCategoryID()
     * Get posts by category id
     * @param integer $category_id
     * @return
     */
    function getPostsByCategoryID($category_id)
    {
        return (bool)$this->select('blog_posts', '*', 'where post_cat_id = ?', $category_id);
    }
    
    /**
     * Apps_MyBlog::getAllPosts()
     * Get all blog posts .
     * @param integer $start
     * @param integer $length
     * @return bool
     */
    function getAllPosts($start = null, $length = null)
    {
        if(!empty($start) and !empty($length)) $limit = ' LIMIT '.(int)$start.','.(int)$length;
        else $limit = null;
        return (bool)$this->select('blog_posts', '*', $limit);
    }
    
    /**
     * Apps_MyBlog::newCategory()
     * Create new blog category .
     * @param string $title
     * @return bool
     */
    function newCategory($title)
    {
        return (bool)$this->insert('blog_cats', 'cat_title',array($title));
    }
    
    /**
     * Apps_MyBlog::deleteCategory()
     * Delete Categor(y/ies) .
     * @param integer $id
     * @return bool
     */
    function deleteCategory($id)
    {
        $this->delete('blog_cats', 'where cat_id in ('.implode(', ', array_fill(1, count((array)$id), '?')).')', (array)$id);
        return (bool)$this->delete('blog_posts', 'where post_id in ('.implode(', ', array_fill(1, count((array)$id), '?')).')', (array)$id);
    }
    
    /**
     * Apps_MyBlog::editCategory()
     * Edit category .
     * @param integer $category_id
     * @param string $new_title
     * @return bool
     */
    function editCategory($category_id, $new_title)
    {
        return (bool)$this->update('blog_cats', array('cat_title' => $new_title), 'where cat_id = ?', $category_id);
    }
    
    /**
     * Apps_MyBlog::getCategories()
     * Get All Categries .
     * @param integer $start
     * @param integer $length
     * @return bool
     */
    function getCategories($start = null, $length = null)
    {
        if(!empty($start) and !empty($length)) $limit = ' LIMIT '.(int)$start.','.(int)$length;
        else $limit = null;
        return (bool)$this->select('blog_cats', '*', $limit);
    }
    
    /**
     * Apps_MyBlog::tables()
     * Build tables .
     * @return void
     */
    protected function tables()
    {
           $this->query('
                CREATE TABLE IF NOT EXISTS blog_posts(
                    post_id         INT NOT NULL AUTO_INCREMENT,
                    post_title      CHAR(100) NOT NULL, 
                    post_content    TEXT NOT NULL,
                    post_author     CHAR(100) NOT NULL,
                    post_time       CHAR(50) NOT NULL,
                    post_cat_id     INT NOT NULL,
                    PRIMARY KEY(post_id),
                    FULLTEXT INDEX(post_title, post_content)
                ) ENGINE MYISAM DEFAULT CHARSET UTF8;
           ');
           
           $this->query('
                CREATE TABLE IF NOT EXISTS blog_cats(
                    cat_id         INT NOT NULL AUTO_INCREMENT,
                    cat_title      CHAR(100) NOT NULL,
                    PRIMARY KEY(cat_id),
                    UNIQUE KEY(cat_title)
                ) ENGINE MYISAM DEFAULT CHARSET UTF8;
           ');
    }
}
