<?php
# define package usage
use libs\vecni\Response;
use libs\vecni\Request;
use libs\vecni\Vecni;
use libs\user\User;
use libs\mysql\PDOConnector;

$vecni = new Vecni();
User::start_session();

$twig = Vecni::twig_loader();
Response::init();

# added global variables to twig
$twig->addGlobal("config", $vecni->get_configs());
$twig->addGlobal("host", Vecni::$host);
$twig->addGlobal("title", "Tattle Tale");
if(User::is_login()){
    $twig->addGlobal("user", User::get_current_user());
}

$stmt = PDOConnector::$db->prepare("Select * from genres order by genre_id");
$stmt->execute();
$genres = $stmt->fetchAll(\PDO::FETCH_ASSOC);
$twig->addGlobal("genres", $genres);

$users = array();

#allow call to static functions
function staticCall($class, $function, $args = array()){
    if (class_exists($class) && method_exists($class, $function))
        return call_user_func_array(array($class, $function), $args);
    return null;
}

$twig->addFunction('staticCall', new Twig_Function_Function('staticCall'));

Vecni::set_route("/", "welcome");
Vecni::set_route("/home", "welcome");
function welcome(){
    global $twig;
    $sql = "SELECT books.* , genre, CONCAT(first_name,  ' ', last_name) AS author FROM books LEFT JOIN authors ON authors.author_id = books.author_id LEFT JOIN genres ON genres.genre_id = books.genre_id";
    $stmt = PDOConnector::$db->prepare($sql);
    $stmt->execute();
    $books = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return $twig->render('home.html',
                  array(
                    "html_class"=>"welcome",
                    "title"=>Vecni::$BRAND_NAME,
                    "books"=>$books
                  )
              );
}

function book_search($text, $id=null){
    $sql = "select * from book_information  where book_title like '%$text%' or author_id = $id or author like '%World War I%' or book_id = $id or genre like '%$text%' or genre_id = $id";
    $stmt = PDOConnector::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

function book_by_id($id){
    $sql = "select * from book_information where book_id = $id";
    $stmt = PDOConnector::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

function book_by_genre($genre, $id=""){
    $sql = "select * from book_information where genre = 'genre' or genre_id = $id";
    $stmt = PDOConnector::$db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

Vecni::set_route("/books/view/{book_id}/{book_title}", "book_view");
function book_view(){
    global $twig;
    $book_title = Request::GET("book_title");
    $book_id = Request::GET("book_id");
    $book = book_by_id($book_id);
    return $twig->render('book.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"$book_title",
                    "book"=>$book
                  )
              );
}

Vecni::set_route("/book/add", "book_add");
function book_add(){
    global $twig;
    return $twig->render('book_add.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"Add Book",
                  )
              );
}

Vecni::set_route("/book/process/add", "book_registration");
function book_registration(){
    if(($book_title = Request::POST("book_title")) &&
       ($author = Request::POST("author")) &&
       ($genre_id = Request::POST("genre_id"))
      ){
        $name = explode(" ", $author);
        $first_name = $name[0];
        if(count($name) > 1)
            $last_name = $name[1];
        else
            $last_name = "";
        $cover = Request::POST("cover_photo", Vecni::$host."/static/img/photo/default_book_cover.jpg");
        $sql = "Select author_id from authors where first_name = '$first_name' and last_name = '$last_name'";
        $stmt = PDOConnector::$db->prepare($sql);
        $stmt->execute();
        $author_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(empty($author_id)){
            $sql2 = "insert into authors(first_name, last_name) values('$first_name', '$last_name')";
            $stmt = PDOConnector::$db->prepare($sql2);
            $stmt->execute();
            $author_id = PDOConnector::$db->lastInsertId();
        }
        $sql3 = "insert into books(book_title, author_id, genre_id, cover_photo) values('$book_title', $author_id, $genre_id, '$cover')";
        $stmt = PDOConnector::$db->prepare($sql3);
        $stmt->execute();
        $author_id = PDOConnector::$db->lastInsertId();
        return $author_id;
    }
}


Vecni::set_route("/genre/view/{genre_id}/{genre}", "genre_view");
function genre_view(){
    global $twig, $books;
    $genre = Request::GET("genre");
    $genre_id = Request::GET("genre_id");
    $category_books = book_by_genre($genre, $genre_id);
    return $twig->render('book_filter.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"Books in $genre - genre",
                    "books"=>$category_books,
                    "menu_active"=>"genre/$genre"
                  )
              );
}


Vecni::set_route("/genre/add/process", "genre_add_process");
function genre_add_process(){
    if(($genre = Request::POST("genre")) &&
       ($genre_id = Request::POST("genre_id"))
    ){
        if(!is_int($genre_id)){
            return Response::abort("Genre ID must be number");
        }
        $sql = "INSERT INTO genres(genre_id, genre) VALUES($genre_id, '$genre')";
        PDOConnector::$db->exec($sql);
        $stmt = PDOConnector::$db->prepare("select * from genres");
        $stmt->execute();
        $genres = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return Response::json_feed($genres);
    }
}


Vecni::set_route("/author/{author}", "author_view");
function author_view(){
    global $twig, $books, $authors;
    $author = Request::GET("author");
    $author_books = array();
    foreach($books as $book){
        if($book["author"] == $author){
            array_push($author_books, $book);
        }
    }
    $description=$authors[$author];
    return $twig->render('book_filter.html',
                  array(
                    "html_class"=>"author",
                    "title"=>"Books by $author",
                    "books"=>$author_books,
                    "description"=>$description,
                    "menu_active"=>"author/$author"
                  )
              );
}


Vecni::set_route("/recent", "recent_view");
function recent_view(){
    global $twig, $books;
    $recent_books = array();
    foreach($books as $book){
        if((time() - 1000) < $book["date_added"]){
            array_push($recent_books, $book);
        }
    }
    return $twig->render('book_filter.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"Books recently added",
                    "books"=>$recent_books,
                    "menu_active"=>"recent"
                  )
              );
}


Vecni::set_route("/popular", "popular_view");
function popular_view(){
    global $twig, $books;
    $popular_books = array();
    foreach($books as $book){
        if(4 < $book["date_added"]){
            array_push($popular_books, $book);
        }
    }
    return $twig->render('book_filter.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"Popular Books",
                    "books"=>$popular_books,
                    "menu_active"=>"popular"
                  )
              );
}

Vecni::set_route("/books", "book_list");
function book_list(){
    global $twig, $books;
    $list_books = array();
    if($search = Request::Post("q")){
        $searchq = strtolower($search);
        foreach($books as $book){
            $result = (strrpos(strtolower(" ".$book["book_title"]), $searchq) || strrpos(strtolower(" ".$book["author"]), $searchq) || strrpos(" ".strtolower($book["genre"]), $searchq));
            if($result !== false) {
                array_push($list_books, $book);
            }
        }
        return $twig->render('book_filter.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"Results for $search",
                    "books"=>$list_books,
                    "search"=>$search,
                    "menu_active"=>"popular"
                  )
              );
    }else{
        Vecni::redirect();
    }
}


Vecni::set_error_route("error404");
function error404(){
    if(!Request::is_async()){
        global $twig;
        return $twig->render('404.html');
    }else{
        return "404 - Not Found";
    }
}


/**
* Sign in page for users
*/
Vecni::set_route("/user/signin", "signin_require");
function signin_require($message=""){
    global $twig;
    return $twig->render('user_signin.html',
              array(
                "html_class"=>"signin",
                "title"=>"Signin Required",
                "message"=>$message
              )
          );
}

/**
* Sign in processing for users
*/
Vecni::set_route("/user/signin/process", "process_login");
function process_login(){
    if(!empty($_POST['email']) && !empty($_POST['password'])){
        $email = $_POST['email'];
        $pass = $_POST['password'];
        $status = User::login($email, $pass);
        if(Request::is_async()){
            if($status){
                return Response::json_response(200, $email);
            }else{
                return Response::abort("$email, does not exists in our system. Please register for account if you don't have one");
            }
        }else{
            if($status){
                Vecni::nav_back();
            }else{
                signin_require();
            }
        }
    }
}


/**
* Registration page for users
*/
Vecni::set_route("/user/registration", "reg_request");
function reg_request($message=""){
    global $twig;
    if(User::is_login()){
        Vecni::redirect();
    }
    return $twig->render('user_registration.html',
                        array("html_class"=>"user-registration",
                             "title"=>"Registration",
                             )
                        );
}

/**
* Registration processing for users
*/
Vecni::set_route("/user/registration/process", "register");
function register(){
    global $user;
    if(($first_name = Request::POST('first_name')) &&
       ($last_name =  Request::POST('last_name')) &&
       ($password = Request::POST('password')) &&
       ($email = Request::POST('email'))){
        $new_user = new User();
        $new_user->first_name = $first_name;
        $new_user->last_name = $last_name;
        if($dob = Request::POST('dob')){
            $new_user->dob  = $dob;
        }else{
            $new_user->dob = "0000-00-00";
        }
        $new_user->gender = Request::POST('gender', "other");
        $status = $new_user->register($email, $password);
        if(Request::is_async()){
            if($status){
                return Response::json_response(200, $email);
            }else{
                return Response::abort("This accound has already been registered");
            }
        }else{
            if($status){
                Vecni::redirect();
            }else{
                Vecni::redirect();
            }
        }
    }
}

Vecni::set_route("/facebooklogin", "login_with_social_network");
Vecni::set_route("/googleplus", "login_with_social_network");
Vecni::set_route("/twitter", "login_with_social_network");
function login_with_social_network(){
    global $user;
    if(User::is_login()){
        Vecni::redirect();
    }
    if(!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['social_network']) && !empty($_POST['social_network_id']) && !empty($_POST['email'])){
        $new_user = new User();
        $new_user->first_name = $_POST['first_name'];
        $new_user->last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $new_user->dob  = DateTime::createFromFormat('m/d/Y',
                                           $_POST['dob']);
        $new_user->gender = $_POST['gender'];
        if(!empty($_POST['school'])){
            $new_user->school = $_POST['school'];
        }
        $account_type = $_POST['social_network'];
        $account_id = $_POST['social_network_id'];
        $status = $new_user->login_with_social_network($email, $account_type, $account_id);
        if($status){
            return Response::json_response(200, $email);
        }else{
            return Response::json_response(204, "Something went wrong");
        }
    }
}


/**
* Log out users out of Tattle Tale
* @redirect page welcome
*/
Vecni::set_route("/logout", "log_out");
function log_out(){
    global $twig;
    if(User::is_login()){
        User::log_out();
        $twig->addGlobal("user", new User());
    }
    Vecni::redirect();
}


?>


