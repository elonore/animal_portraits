<?php
ob_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
$page = 'Accueil';
$message = '';
require 'inc/connect.php';
require 'inc/header.php';
if (isset($_POST['submit-signup'])){
    $user_email = htmlspecialchars($_POST['email_register']);
    $user_pass = htmlspecialchars($_POST['password_register']);
    $user_pass2 = htmlspecialchars($_POST['password_confirm_register']);
    $user_name = htmlspecialchars($_POST['name_register']);
    $user_firstname = htmlspecialchars($_POST['firstname_register']);
    $type_user = htmlspecialchars($_POST['type_user']);
    $admin = 0;
    if($sql = $db->query("SELECT * FROM users WHERE user_email = '$user_email'")){
        $compteur = $sql->rowCount();
        if($compteur != 0){
            $message = "<div class ='alert alert-danger'> Il y a déja un compte possédant cet e-mail </div>";
        }elseif(!empty($user_email) && !empty($user_pass)){
            if($user_pass == $user_pass2){
                $user_pass = password_hash($user_pass, PASSWORD_DEFAULT);
                $sth = $db->prepare("INSERT INTO users (user_email, user_password, user_firstname, user_lastname, admin, type, user_phone) VALUES (:email,:password,:firstname,:lastname,:admin_status, :type, :phone)");
                $sth->bindValue(':email',$user_email);
                $sth->bindValue(':password',$user_pass);
                $sth->bindValue(':firstname',$user_firstname);
                $sth->bindValue(':lastname',$user_name);
                $sth->bindValue(':admin_status',$admin);
                $sth->bindValue(':phone',NULL);
                $sth->bindValue(':type',$type_user);
                $sth->execute();
                $message = "<div class ='alert alert-success'> Votre compte à bien été crée ! Connectez vous ! </div>";

            }else{
                $message = "<div class ='alert alert-danger'> Les mots de passes ne concordent pas </div>";
            }
        }else{
            $message = "<div class ='alert alert-danger'> Veuillez remplir les champs correspondants </div>";
        }
    }else{
        $message = "<div class ='alert alert-danger'> Une erreur vient de se produire.</div>";
    }
}
if (isset($_POST['btn-connect'])) {
    $user_email = htmlspecialchars($_POST['email_connect']);
    $user_pass = htmlspecialchars($_POST['password_connect']);
    if ($sql = $db->query("SELECT * FROM users WHERE user_email = '$user_email'")) {
        if ($row = $sql->fetch()) {
            $db_id = $row['id'];
            $db_email = $row['user_email'];
            $db_password = $row['user_password'];
            $db_admin = $row['admin'];
            $db_firstname = $row['user_firstname'];
            $db_lastname = $row['user_lastname'];
            $db_phone = $row['user_phone'];
            $db_type = $row['type'];
            if (password_verify($user_pass, $db_password)) {
                $_SESSION['id'] = $db_id;
                $_SESSION['email'] = $db_email;
                $_SESSION['admin'] = $db_admin;
                $_SESSION['firstname'] = $db_firstname;
                $_SESSION['lastname'] = $db_lastname;
                $_SESSION['phone'] = $db_phone;
                $_SESSION['type'] = $db_type;

                header('Location: pannel/home.php');
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Password are not correct.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Veuillez saisir un email valide.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Une erreur est survenu. Merci de réesayer</div>";
    }
}

echo $message;
require 'inc/nav/nav_users.php';

?>



<!-- Modal Connection -->
<div class="modal fade" id="modalConnect" tabindex="-1" aria-labelledby="modalConnect" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Connexion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="email_connect" placeholder="Email"/>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password_connect" placeholder="Mot de passe"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Retour</button>
                    <button type="submit" class="btn_login btn btn-danger" name="btn-connect">Se connecter</button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- Modal Sign Up -->
<div class="modal fade" id="modalSignUp" tabindex="-1" aria-labelledby="modalSignUp" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Inscription</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php" method="post">

            <div class="modal-body">
                <div class="form-group">
                    <p>Je suis:</p>
                    <label for="type_user">Locataire
                        <input type="radio" name="type_user" value="loc">
                    </label>
                    <label for="type_user">Propriétaire
                        <input type="radio" name="type_user" value="prop">
                    </label>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="name_register" placeholder="Nom"/>
                </div>
                <div class="form-group">
                <input type="text" class="form-control" name="firstname_register" placeholder="Prenom"/>
                </div>
                <div class="form-group">
                <input type="text" class="form-control" name="email_register" placeholder="Email"/>
                </div>
                <div class="form-group">
                <input type="password" class="form-control" name="password_register" placeholder="Mot de passe"/>
                </div>
                <div class="form-group">
                <input type="password" class="form-control" name="password_confirm_register"
                           placeholder="Confirmer le mot de passe"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Retour</button>
                <button type="submit" class="btn btn-danger btn_sign_up" name="submit-signup">S'inscrire</button>
            </div>
            </form>

        </div>
    </div>
</div>
<div class="jumbotron jumbotron-fluid bg-jumbotron">
    <div class="container">
        <h1 class=" display-4">Bienvenue sur Real Escape</h1>
        <input type="text" placeholder="Votre prochaine destination..."
               class="form-control w-75" id="search_home">
        <div class="list-group list-group-horizontal mt-3">
            <!--
            <div class="dropdown show">
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Prix du loyer
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <div class="form-group px-4 py-3">
                        <input type="text" class="form-control">
                        <br>
                        <input type="text" class="form-control">
                    </div>
                </div>
            </div>
            -->
            <div class="dropdown show mx-2">
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Energie
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <div class="form-group p-2 div_elec">
                        <label for="elec">Electricité</label>
                        <input type="radio" name="energie" class="common_selector energie" id="elec" value="elec" checked>
                        <br>
                        <label for="bois">Bois</label>
                        <input type="radio" name="energie" class="common_selector energie" id="bois" value="bois">
                        <br>
                        <label for="gaz">Gaz</label>
                        <input type="radio" name="energie" class="common_selector energie" id="gaz" value="gaz">
                    </div>
                </div>
            </div>
            <div class="dropdown show">
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Meubler
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <div class="form-group p-2">
                        <label>Oui</label>
                        <input type="radio" name="meuble" class="common_selector meuble" value="1" checked>
                        <br>
                        <label for="bois">Non</label>
                        <input type="radio" name="meuble" class="common_selector meuble" value="0">
                    </div>
                </div>
            </div>
            <button type="button" id="btn-search-home" class="btn btn-dark mx-2" name="search">
                Trier
            </button>
        </div>
    </div>
</div>

<div id="annonces">
    <section id="annonces_section">
        <article id="container_annonces">
        </article>
    </section>
</div>
<script type="text/javascript" src="http://localhost/real_estate/assets/js/main.js"></script>

<script>
    fetch_annonces();
</script>

</div>
