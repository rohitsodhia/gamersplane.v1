<?php
// Run: php -d short_open_tag=1 tests/forum-character-links.php
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
define('FILEROOT', dirname(__DIR__));
require FILEROOT . '/includes/characters/Character.class.php';
class User { public static function inactive($lastActivity) { return ''; } }
class ForumLinkCharacter extends Character {
    const SYSTEM = 'custom';
    public function __construct() { $this->characterID = 42; $this->name = 'Test character'; }
    public function checkPermissions($userID = null) { throw new RuntimeException('Rendering a link must not require sheet edit permission'); }
}
$character = new ForumLinkCharacter();
$author = (object) ['userID' => 7, 'username' => 'Author', 'lastActivity' => null];
foreach (['owner' => 7, 'other player' => 8, 'guest' => null] as $role => $id) {
    $currentUser = (object) ['userID' => $id];
    foreach ([false, true] as $isGM) {
        ob_start(); $character->getForumTop($author, $isGM); $html = ob_get_clean();
        if (!str_contains($html, '<a href="/characters/custom/42/">Test character</a>') || !str_contains($html, '<a href="/user/7/" class="username">Author</a>') || str_contains($html, 'gm_icon.png') !== $isGM) {
            throw new RuntimeException('Incorrect forum header for ' . $role);
        }
    }
}
echo "PASS: character links for owner, other player and guest; author link and GM badge preserved\n";
