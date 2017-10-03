<?php
/**
 * WordPressの基本設定
 *
 * このファイルは、インストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さずにこのファイルを "wp-config.php" という名前でコピーして
 * 直接編集して値を入力してもかまいません。
 *
 * このファイルは、以下の設定を含みます。
 *
 * * MySQL 設定
 * * 秘密鍵
 * * データベーステーブル接頭辞
 * * ABSPATH
 *
 * @link http://wpdocs.osdn.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86
 *
 * @package WordPress
 */

// 注意:
// Windows の "メモ帳" でこのファイルを編集しないでください！
// 問題なく使えるテキストエディタ
// (http://wpdocs.osdn.jp/%E7%94%A8%E8%AA%9E%E9%9B%86#.E3.83.86.E3.82.AD.E3.82.B9.E3.83.88.E3.82.A8.E3.83.87.E3.82.A3.E3.82.BF 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - この情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', 'wp_shakyo');

/** MySQL データベースのユーザー名 */
define('DB_USER', 'root');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'root');

/** MySQL のホスト名 */
define('DB_HOST', 'localhost');

/** データベースのテーブルを作成する際のデータベースの文字セット */
define('DB_CHARSET', 'utf8');

/** データベースの照合順序（ほとんどの場合変更する必要はありません）*/
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '8UMHjlo05ts0EN7S?*,YYGzJ @I,T} M.d-BLd/IT~u3?,xb<9Jhh?q>!|N2U_&t');
define('SECURE_AUTH_KEY',  '0-P?MaSA/c0znrh rS`>L9iG*v/.-=_8POrWL-lqD]Ri9aE+*onvFWZlI//nYu;T');
define('LOGGED_IN_KEY',    'X6HQA2(. F^?J=}/_m{KFU4Dvb<^H-fsrvR!ll5!,_><&dndQ/~hg]hy]W{0JU6[');
define('NONCE_KEY',        'WYFubv>-<]`edg=&k6Ai0hgTe/ s1>_-_G+1RjqQfsaYU!MJO,OT$E`vKip1-@J:');
define('AUTH_SALT',        'V!:Dq##$+?qU|du-pSV=3dK*H<$ aO*BR^3;#?2%LgRW$E8qg>K {O#l_H+ExUsI');
define('SECURE_AUTH_SALT', '!D9Zd^}x{EJkasMo&h`kd,HlXV= h0d&anvYZ0jFQ F$|#)CqOB(`~d%@_npL5wh');
define('LOGGED_IN_SALT',   '1!~y74:.xC~].ac2/z4BP4#*c!EK:0Ncqh .ln7vbw4d?_n0nq.v_Bq|j;kuONoM');
define('NONCE_SALT',       'cN&}MkT 92!(qOVBC,:IjM(^$%j@Cf&pSqe&{A_et mvpMzm1Y!$zN_e+{L-nezc');

/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'wp_';

/**
 * 開発者へ: WordPressデバッグモード
 *
 * この値を true にすると、開発中に注意（notice）を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 *
 * その他のデバッグに利用できる定数については Codex をご覧ください。
 *
 * @link http://wpdocs.osdn.jp/WordPress%E3%81%A7%E3%81%AE%E3%83%87%E3%83%90%E3%83%83%E3%82%B0
 */
// define('WP_DEBUG', false);
define('WP_DEBUG', true);

/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** Set up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
