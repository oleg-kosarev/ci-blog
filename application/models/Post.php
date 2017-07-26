<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Model{

	var $table = 'posts';


	function find($limit = null, $offset = 0, $user_id = null, $q = null){
// 		SELECT pt.post_id AS `post_id`, GROUP_CONCAT(t.tag) AS `tags`
// FROM post_tags AS pt
// INNER JOIN tags AS t ON pt.tag_id = t.tag_id
// GROUP BY `post_id`
		$this->db->select('posts.*,users.username');
        $this->db->join('users', 'users.id = posts.user_id');
        if ($q != null) {
            $this->db->like('title', $q);
        }
        if($user_id != null){
        	$this->db->where('user_id',$user_id);
        }
        $this->db->where('type','post');
        $this->db->limit($limit, $offset);
        $this->db->order_by('published_at', 'desc');
        $query = $this->db->get($this->table);

        return $query->result_array();
	}

	function find_active($limit = null, $offset = 0, $q = null){
		$this->db->select('posts.*,users.username');
        $this->db->join('users', 'users.id = posts.user_id');
        if ($q != null) {
            $this->db->like('title', $q);
        }
        $this->db->where('status',1);
        $this->db->where('type','post');
        $this->db->limit($limit, $offset);
        $this->db->order_by('published_at', 'desc');
        $query = $this->db->get($this->table);
        //return $query->result_array();
        foreach ($query->result_array() as $post) {
            $postId = $post["id"];
            $arrItems["items"][$postId]["id"] = $post["id"];
            $arrItems["items"][$postId]["title"] = $post["title"];
            $arrItems["items"][$postId]["slug"] = $post["slug"];
            $arrItems["items"][$postId]["featured_image"] = $post["featured_image"];
            $arrItems["items"][$postId]["username"] = $post["username"];
            $arrItems["items"][$postId]["published_at"] = $post["published_at"];
            $arrItems["items"][$postId]["tags"] = $this->getAllTags($postId);
            $arrItems["items"][$postId]["preview_body"] = $post["preview_body"];
            $arrItems["items"][$postId]["hidden_body"] = $post["body"];
            }
            return $arrItems;
	}

	function find_by_category($slug,$limit = null, $offset = 0){
		$this->db->select('p.*,u.username');
		$this->db->join('categories c','pc.category_id=c.id');
		$this->db->join('posts p','pc.post_id=p.id');
		$this->db->join('users u','p.user_id=u.id');
		$this->db->where('p.status',1);
		$this->db->where('c.slug',$slug);
		$this->db->group_by('pc.post_id');
		$this->db->order_by('p.published_at','desc');
		$posts = $this->db->get('posts_categories pc',$limit,$offset)->result_array();
                foreach ($posts as $post) {
                    $postId = $post["id"];
                    $arrItems["items"][$postId]["id"] = $post["id"];
                    $arrItems["items"][$postId]["title"] = $post["title"];
                    $arrItems["items"][$postId]["slug"] = $post["slug"];
                    $arrItems["items"][$postId]["featured_image"] = $post["featured_image"];
                    $arrItems["items"][$postId]["username"] = $post["username"];
                    $arrItems["items"][$postId]["published_at"] = $post["published_at"];
                    $arrItems["items"][$postId]["tags"] = $this->getAllTags($postId);
                    $arrItems["items"][$postId]["preview_body"] = $post["preview_body"];
                    $arrItems["items"][$postId]["hidden_body"] = $post["body"];
                    //echo $post["id"]." ---- ".$post["title"]."<br/>";
                }
                //return $posts;
                return $arrItems;
        }

	function find_by_tag($slug,$limit = null, $offset = 0){
		$this->db->select('p.*,u.username');
		$this->db->join('tags c','pc.tag_id=c.id');
		$this->db->join('posts p','pc.post_id=p.id');
		$this->db->join('users u','p.user_id=u.id');
		$this->db->where('p.status',1);
		$this->db->where('c.slug',$slug);
		$this->db->group_by('pc.post_id');
		$this->db->order_by('p.published_at','desc');
		$posts = $this->db->get('posts_tags pc',$limit,$offset)->result_array();
                foreach ($posts as $post) {
                    $postId = $post["id"];
                    $arrItems["items"][$postId]["id"] = $post["id"];
                    $arrItems["items"][$postId]["title"] = $post["title"];
                    $arrItems["items"][$postId]["slug"] = $post["slug"];
                    $arrItems["items"][$postId]["featured_image"] = $post["featured_image"];
                    $arrItems["items"][$postId]["username"] = $post["username"];
                    $arrItems["items"][$postId]["published_at"] = $post["published_at"];
                    $arrItems["items"][$postId]["tags"] = $this->getAllTags($postId);
                    $arrItems["items"][$postId]["preview_body"] = $post["preview_body"];
                    $arrItems["items"][$postId]["hidden_body"] = $post["body"];
                    //echo $post["id"]." ---- ".$post["title"]."<br/>";
                }
                //return $posts;
                return $arrItems;
        }

	function create($post){
            //$post['slug'] = url_title($post['title'], '-', true);    
            $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
            $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
            $post['slug'] = strtolower(str_replace(" ", "-", str_replace($rus, $lat, $post['title'])));
            $post['preview_body'] = trim(preg_replace('/\s\s+/', ' ', $post['preview_body']));
        $post['body'] = trim(preg_replace('/\s\s+/', ' ',$post['body']));
		$this->db->insert($this->table, $post);
	}

	function update($post,$id){
            //$post['slug'] = url_title($post['title'], '-', true);
            $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
            $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
            $post['slug'] = strtolower(str_replace(" ", "-", str_replace($rus, $lat, $post['title'])));
            $post['preview_body'] = trim(preg_replace('/\s\s+/', ' ', $post['preview_body']));
		$post['body'] = trim(preg_replace('/\s\s+/', ' ',$post['body']));
		$this->db->where('id',$id);
		$this->db->update($this->table,$post);
	}

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete($this->table);
	}

	function find_by_id($id){
		$this->db->where('id',$id);
		return $this->db->get($this->table,1)->row_array();
	}

	function find_by_slug($slug){
		$this->db->select('posts.*,users.username');
        $this->db->join('users', 'users.id = posts.user_id');
		$this->db->where('slug',$slug);
		return $this->db->get($this->table,1)->row_array();
	}

	function all_urls(){
		$posts = $this->db->select('id,title,slug')->where(array('status' => 1))->order_by('id','desc')->get($this->table)->result_array();
		$all_urls = array();
		if(!empty($posts)){
			foreach($posts as $post){
				$all_urls['read/'.$post['slug']] = $post['title'];
			}
		}
		
		return $all_urls;
	}
        function getAllTags($idPost) {
            $this->db->select('tags.name, tags.slug');
            $this->db->join('posts_tags', 'posts_tags.tag_id = tags.id');
            $this->db->where("posts_tags.post_id = $idPost");
            $this->db->where("tags.status = 1");
            $tags = $this->db->get('tags')->result_array();
            return $tags;
    }

}