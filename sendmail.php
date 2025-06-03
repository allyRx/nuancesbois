<?php
$destinataire = "paysabois@gmail.com";
$fromEmail = "no-reply@nuancesbois.com"; // ✅ À modifier avec un email réel lié à ton domaine

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $demande = htmlspecialchars($_POST["f1-c0-demande_1"] ?? '');
    $nom = htmlspecialchars($_POST["f1-c0-nom_2"] ?? '');
    $telephone = htmlspecialchars($_POST["f1-c0-telephone_3"] ?? '');
    $email = filter_var($_POST["f1-c0-e-mail_4"] ?? '', FILTER_SANITIZE_EMAIL);
    $adresseChantier = htmlspecialchars($_POST["f1-c0-adresse-du-chantier_5"] ?? '');
    $precisions = htmlspecialchars($_POST["f1-c0-precisions-_6"] ?? '');
    $rgpd = isset($_POST["rgpd-check-1"]) ? "Oui" : "Non";

    $subject = "Formulaire de contact : $demande";

    $messageText = "
Demande : $demande
Nom : $nom
Téléphone : $telephone
Email : $email
Adresse du chantier : $adresseChantier
Précisions : $precisions
Consentement RGPD : $rgpd
";

    // Détection de pièce jointe
    if (!empty($_FILES['f1-c0-joindre-un-fichier-_7']['tmp_name'])) {
        $file_tmp = $_FILES['f1-c0-joindre-un-fichier-_7']['tmp_name'];
        $file_name = $_FILES['f1-c0-joindre-un-fichier-_7']['name'];
        $file_type = $_FILES['f1-c0-joindre-un-fichier-_7']['type'];
        $file_content = chunk_split(base64_encode(file_get_contents($file_tmp)));

        $boundary = md5(uniqid(time()));

        $headers = "From: Mon Site <$fromEmail>\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $messageText . "\r\n\r\n";

        $message .= "--$boundary\r\n";
        $message .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $message .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= $file_content . "\r\n";
        $message .= "--$boundary--";
    } else {
        // Pas de pièce jointe
        $headers = "From: Mon Site <$fromEmail>\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $message = $messageText;
    }

    $send = mail($destinataire, $subject, $message, $headers);

    if ($send) {
        echo "Message envoyé avec succès.";
    } else {
        echo "Erreur lors de l'envoi du message.";
    }
} else {
    echo "Accès non autorisé.";
}
