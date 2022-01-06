<?php

include_once 'httpsocket.php';

$sock=new HTTPSocket;
$sock->connect('ssl://$directadmin_server', 2222);
$sock->set_login('$directadmin_username', '$directadmin_password');

//list users
$sock->query('/CMD_API_SHOW_USERS');
$users=$sock->fetch_parsed_body();

foreach($users['list'] as $user) {
    //get domains for user
    $sock->query(
        '/CMD_API_SHOW_USER_DOMAINS',
        array(
            'user'=>$user
        )
    );

$domains=$sock->fetch_parsed_body();

echo str_replace('_', '.', $domain).'<br>';

//print_r($domains);

$sock2=new HTTPSocket;
$sock2->connect('ssl://fqdn', 2222);
$sock2->set_login('admin|'.$user, 'pass');

    foreach($domains as $domain=>$data) {
        //get domain pointers for domain
        $sock2->query(
            '/CMD_API_DOMAIN_POINTER',
            array(
                'domain'=>str_replace('_', '.', $domain)
            )
        );

        $domain_pointers=$sock2->fetch_parsed_body();

        print_r($domain_pointers);
    }
}
