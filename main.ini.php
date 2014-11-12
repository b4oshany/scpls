<?php
# define package usage
use libs\vecni\Response;
use libs\vecni\Request;
use libs\vecni\Vecni;
use libs\user\User;

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


#allow call to static functions
function staticCall($class, $function, $args = array()){
    if (class_exists($class) && method_exists($class, $function))
        return call_user_func_array(array($class, $function), $args);
    return null;
}

$twig->addFunction('staticCall', new Twig_Function_Function('staticCall'));


function get_books(){
    $books = file_get_contents("books");
    $books = json_decode($books);
    return $books;
}

function get_authors(){
    $authors = file_get_contents("books");
    $authors = json_decode($authors);
    return $authors;
}

function book_search($book_key, $book_value){
    global $books;
    foreach($books as $book){
        if($book[$book_key] == $book_value){
            return $book;
        }
    }
    return null;
}

Vecni::set_route("/", "welcome");
Vecni::set_route("/home", "welcome");
function welcome(){
    global $twig, $books;
    if(User::is_login()){
        return $twig->render("home.html",
                    array(
                        "html_class"=>"welcome"
                    ));
    }else{
        return $twig->render('home.html',
                      array(
                        "html_class"=>"welcome",
                        "title"=>Vecni::$BRAND_NAME,
                        "books"=>$books
                      )
                  );
    }
}


Vecni::set_route("/books/{book_title}", "book_view");
function book_view(){
    global $twig, $books;
    $book_title = Request::GET("book_title");
    $book = book_search("book_title", $book_title);
    return $twig->render('book.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"$book_title",
                    "book"=>$book
                  )
              );
}


Vecni::set_route("/genre/{genre}", "genre_view");
function genre_view(){
    global $twig, $books;
    $genre = Request::GET("genre");
    $category_books = array();
    foreach($books as $book){
        if($book["genre"] == $genre){
            array_push($category_books, $book);
        }
    }
    return $twig->render('book_filter.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"Books in $genre - genre",
                    "books"=>$category_books,
                    "menu_active"=>"genre/$genre"
                  )
              );
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


