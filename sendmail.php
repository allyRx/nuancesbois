<?php
// Paramètres de destination
$destinataire = "estalbertcontact@gmail.com"; // Remplacez par votre e-mail réel

// Vérifier que le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Récupération des champs (avec un minimum de sécurité)
    $demande = htmlspecialchars($_POST["f1-c0-demande_1"] ?? '');
    $nom = htmlspecialchars($_POST["f1-c0-nom_2"] ?? '');
    $telephone = htmlspecialchars($_POST["f1-c0-telephone_3"] ?? '');
    $email = filter_var($_POST["f1-c0-e-mail_4"] ?? '', FILTER_SANITIZE_EMAIL);
    $adresseChantier = htmlspecialchars($_POST["f1-c0-adresse-du-chantier_5"] ?? '');
    $precisions = htmlspecialchars($_POST["f1-c0-precisions-_6"] ?? '');
    $rgpd = isset($_POST["rgpd-check-1"]) ? "Oui" : "Non";

    // Sujet du mail
    $subject = "Formulaire de contact : $demande";

    // Corps du mail
    $message = "
    Demande : $demande
    Nom : $nom
    Téléphone : $telephone
    Email : $email
    Adresse du chantier : $adresseChantier
    Précisions : $precisions
    Consentement RGPD : $rgpd
    ";

    // En-têtes email
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Pièce jointe (optionnel)
    if (!empty($_FILES['f1-c0-joindre-un-fichier-_7']['tmp_name'])) {
        $file_tmp = $_FILES['f1-c0-joindre-un-fichier-_7']['tmp_name'];
        $file_name = $_FILES['f1-c0-joindre-un-fichier-_7']['name'];
        $file_type = $_FILES['f1-c0-joindre-un-fichier-_7']['type'];
        $file_content = chunk_split(base64_encode(file_get_contents($file_tmp)));

        $boundary = md5(uniqid(time()));

        $headers = "From: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        $message_body = "--$boundary\r\n";
        $message_body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
        $message_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message_body .= $message . "\r\n\r\n";

        $message_body .= "--$boundary\r\n";
        $message_body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $message_body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $message_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message_body .= $file_content . "\r\n";
        $message_body .= "--$boundary--";

        $send = mail($destinataire, $subject, $message_body, $headers);
    } else {
        // Envoi sans pièce jointe
        $send = mail($destinataire, $subject, $message, $headers);
    }

    // Redirection ou message de succès
    if ($send) {
        echo "Message envoyé avec succès.";
    } else {
        echo "Erreur lors de l'envoi du message.";
    }
} else {
    echo "Accès non autorisé.";
}

