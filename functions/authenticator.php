<?php

/**
 * Vérifie si l'email envoyé par l'utilisateur existe dèjà ou non.
 *
 * @param string $email
 * @param PDO $db
 * 
 * @return boolean retourne true si l'email existe et false dans le cas contraire.
 */
function already_exists(string $email, PDO $db): bool
{
    // Vérifier si l'email correspond à celui d'un utilisateur existant dans la base de données.
    $request = $db->prepare("SELECT id FROM user WHERE email=:email");
    $request->bindValue(":email", $email);
    $request->execute();

    // Si c'est le cas,
    if ($request->rowCount() == 1) {
        // la fonction retourne true
        return true;
    }

    // Dans le cas contraire,
    // la fonction retourne false
    return false;
}
