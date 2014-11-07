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

$books = array(
    array("author"=>"Oshane Bailey", "book_title"=>"Vecni", "genre"=>"Technology", "cover_photo"=>"http://www.technaturals.com/wp-content/uploads/2013/08/Technology.jpg"),
    array("author"=>"Andrelle Thompson", "book_title"=>"Marco", "genre"=>"Literature", "cover_photo"=>"http://c.tadst.com/gfx/600x400/galician-literature-day-spain.jpg?1"),
    array("author"=>"Eric NeedHam", "book_title"=>"Best Arts Practises", "genre"=>"Arts and Recreation", "cover_photo"=>"http://www.designindaba.com/sites/default/files/node/page/23/IMG_3015.jpg"),
    array("author"=>"Cloyde McBeth", "book_title"=>"Programming For Dummies", "genre"=>"Computer Science", "cover_photo"=>"http://bobchoat.files.wordpress.com/2014/06/where-is-technology-heading.jpg"),
    array("author"=>"Marc Lynch", "book_title"=>"Work Ethics", "genre"=>"Genera Work", "cover_photo"=>"http://artsonthepeninsula.files.wordpress.com/2012/08/we-value-the-arts1.jpg"),
    array("author"=>"Neil Armstrong", "book_title"=>"Around The Globe", "genre"=>"Geography", "cover_photo"=>"http://sd.keepcalm-o-matic.co.uk/i/keep-calm-and-study-geography-113.png"),
    array("author"=>"Alifumike Adedipe", "book_title"=>"World War I", "genre"=>"History", "cover_photo"=>"http://www.dpcdsb.org/NR/rdonlyres/22300638-C9FC-439B-8040-A7C4A2C5D7EC/87305/history.gif"),
    array("author"=>"Gwen Stephanie", "book_title"=>"Patois Bible", "genre"=>"Language", "cover_photo"=>"https://coe.hawaii.edu/sites/default/files/Letter%20Tree.jpg"),
    array("author"=>"Mike Will", "book_title"=>"New Moon", "genre"=>"Philosophy", "cover_photo"=>"http://www.ithaca.edu/depts/i/Philosophy/28473_photo.jpg"),
    array("author"=>"Cathy Underwood", "book_title"=>"Mind Dynamics", "genre"=>"Psychology", "cover_photo"=>"http://www.ithaca.edu/depts/i/Philosophy/28473_photo.jpg"),
    array("author"=>"Will Smith", "book_title"=>"Steam Turbines", "genre"=>"Technology", "cover_photo"=>"http://3.bp.blogspot.com/-YFPIfvj7h0M/U6OD1EglnOI/AAAAAAAACdY/VEqw1DlqTy4/s1600/technology44-743413.png"),
    array("author"=>"Aca Cop", "book_title"=>"Islam a Religion or Excuse", "genre"=>"Religion", "cover_photo"=>"http://kenanmalik.files.wordpress.com/2013/08/religion-praying.jpg?w=800"),
    array("author"=>"Dean Jones", "book_title"=>"Islam a Religion or Excuse", "genre"=>"Art and Recreation", "cover_photo"=>"http://www.ronnestam.com/wp-content/uploads/2013/03/design-thinking.jpg")
);

#allow call to static functions
function staticCall($class, $function, $args = array()){
    if (class_exists($class) && method_exists($class, $function))
        return call_user_func_array(array($class, $function), $args);
    return null;
}

$twig->addFunction('staticCall', new Twig_Function_Function('staticCall'));


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
    return $twig->render('home.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"$genre",
                    "books"=>$category_books
                  )
              );
}


Vecni::set_route("/author/{author}", "author_view");
function author_view(){
    global $twig, $books;
    $author = Request::GET("genre");
    $author_books = array();
    foreach($books as $book){
        if($book["author"] == $author){
            array_push($author_books, $book);
        }
    }
    return $twig->render('home.html',
                  array(
                    "html_class"=>"book",
                    "title"=>"$author",
                    "books"=>$author_books
                  )
              );
}


/**
* Sign in page for users
*/
Vecni::set_route("/signin", "signin_require");
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
* Registration page for users
*/
Vecni::set_route("/registration", "reg_request");
function reg_request($message=""){
    global $twig;
    if(User::is_login()){
        Vecni::redirect();
    }
    return $twig->render('user_registration.html',
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
Vecni::set_route("/procsignin", "process_login");
function process_login(){
    if(!empty($_POST['email']) && !empty($_POST['password'])){
        $email = $_POST['email'];
        $pass = $_POST['password'];
        $status = User::login($email, $pass);
        if($status){
            return Response::json_response(200, $email);
        }else{
            return Response::abort("$email, does not exists in our system. Please register for account if you don't have one");
        }
    }
}


/**
* Registration processing for users
*/
Vecni::set_route("/procregister", "register");
function register(){
    global $user;
    if($first_name = Request::POST('first_name') && $last_name =  Request::POST('last_name') && $password = Request::POST('password') && $email = Request::POST('email')){
        $new_user = new User();
        $new_user->first_name = $first_name;
        $new_user->last_name = $last_name;
        $new_user->email = $email;
        if($dob = Request::POST('dob')){
            $new_user->dob  = DateTime::createFromFormat('m/d/Y',
                                           $dob);
        }else{
            $new_user->dob = new DateTime("NOW");
        }
        $new_user->gender = Request::POST('gender');
        if($school = Request::POST('school')){
            $new_user->school = $school;
        }
        $status = $new_user->register($email, $password);
        if($status){
            return Response::json_response(200, $email);
        }else{
            return Response::abort("$email, does not exists in our system. Please register for account if you don't have one");
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


