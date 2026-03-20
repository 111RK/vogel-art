<?php
class HomeController
{
    public static function index(): void
    {
        $featured = Database::fetchAll(
            "SELECT * FROM paintings WHERE status = 'available' AND featured = 1 ORDER BY created_at DESC LIMIT 6"
        );
        $recent = Database::fetchAll(
            "SELECT * FROM paintings WHERE status = 'available' ORDER BY created_at DESC LIMIT 8"
        );
        $artistBioRow = Database::fetch("SELECT value FROM settings WHERE `key` = 'artist_bio'");
        $artistBio = $artistBioRow['value'] ?? '';

        $timelineRow = Database::fetch("SELECT value FROM settings WHERE `key` = 'timeline_data'");
        $timeline = [];
        if (!empty($timelineRow['value'])) {
            foreach (explode("\n", $timelineRow['value']) as $line) {
                $parts = array_map('trim', explode('|', $line));
                if (count($parts) >= 3) {
                    $timeline[] = ['year' => $parts[0], 'title' => $parts[1], 'description' => $parts[2]];
                }
            }
        }

        $content = 'home';
        $pageTitle = 'Accueil';
        render('home', compact('featured', 'recent', 'artistBio', 'timeline', 'content', 'pageTitle'));
    }

    public static function about(): void
    {
        $aboutText = Database::fetch("SELECT value FROM settings WHERE `key` = 'about_text'");
        $content = 'about';
        $pageTitle = 'À propos';
        render('about', compact('aboutText', 'content', 'pageTitle'));
    }

    public static function contact(): void
    {
        $content = 'contact';
        $pageTitle = 'Contact';
        render('contact', compact('content', 'pageTitle'));
    }

    public static function contactSubmit(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Erreur de sécurité, veuillez réessayer.');
            redirect('/contact');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            flash('error', 'Tous les champs sont obligatoires.');
            redirect('/contact');
        }

        $contactEmail = Database::fetch("SELECT value FROM settings WHERE `key` = 'contact_email'");
        $to = $contactEmail['value'] ?? 'admin@vogel-art.fr';

        $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
        $subject = "Contact Vogel Art - $name";
        mail($to, $subject, $message, $headers);

        flash('success', 'Votre message a bien été envoyé. Merci !');
        redirect('/contact');
    }

    public static function cgv(): void
    {
        $content = 'cgv';
        $pageTitle = 'Conditions Générales de Vente';
        render('cgv', compact('content', 'pageTitle'));
    }
}
