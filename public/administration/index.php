<?php
require '../../vendor/autoload.php';
require("../../config.php");
require("../db.php");

$app = new \Slim\Slim(array(
    'templates.path' => '../../templates',
));

$app->container->singleton('log', function () {
    $log = new \Monolog\Logger("SimpleCalendarService");
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../../logs/app.log', \Monolog\Logger::DEBUG));
    return $log;
});

$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

$staticText = array('AppName' => CAL_NAME,
                    'Home' => "Übersicht",
                    'Add' => "Eintrag hinzufügen",
                    'PiwikUrl' => PIWIK_URL,
                    'PiwikSiteId' => PIWIK_SITE_ID);

$app->get('/', function () use ($app, $db, $staticText) {
    $app->render('index.html', array_merge( $staticText, array( 'Title' => "Einträge",
                                                                'events' => $db->events()->order("StartDate"))));
});

$app->get('/add', function () use ($app, $staticText) {
    $app->render('edit.html', array_merge($staticText, array( 'Title' => "Eintrag hinzufügen",
                                                              'action' => "add",
                                                              'BtnText' => "Eintrag hinzufügen" )));
});

$app->post('/add', function () use ($app, $db, $staticText) {
    $app->log->info("Adding an event..");
    $data = array("Name" => $app->request->post('Name'),
                  "Location" => $app->request->post('Location'),
                  "StartDate" => $app->request->post('StartDate'),
                  "EndDate" => $app->request->post('EndDate'),
                  "Description" => $app->request->post('Description') );
    $db->events()->insert($data);
    $app->render('status.html', array_merge($staticText, array( 'Title' => "Eintrag hinzufügen",
                                                                'Status' => "Eintrag hinzugefügt" )));
});

$app->get('/del/:id', function ($id) use ($app, $staticText) {
    $id = intval($id);
    if( $id > 0 ) {
        $app->render('del.html', array_merge($staticText , array( 'Title' => "Eintrag löschen",
                                                                  'Msg' => "Wollen Sie wirklich diesen Eintrag löschen?",
                                                                  'Yes' => "Ja",
                                                                  'No' => "Nein",
                                                                  'id' => $id)));
    }
});

$app->get('/del/acc/:id', function ($id) use ($app, $db, $staticText) {
    $id = intval($id);
    if( $id > 0 ) {
        $app->log->info("Deleting [".$id. "]");
        $row = $db->events[$id];
        $row->delete();
        $app->render('status.html', array_merge($staticText , array( 'Title' => "Eintrag löschen",
                                                                     'Status' => "Eintrag gelöscht" )));
    }
});


$app->get('/edit/:id', function ($id) use ($app, $db, $staticText) {
    $id = intval($id);
    if( $id > 0 ) {
        $app->render('edit.html',array_merge($staticText, array( 'Title' => "Eintrag hinzufügen",
                                                                 'Title' => "Eintrag bearbeiten",
                                                                 'BtnText' => "Änderungen speichern",
                                                                 'action' => "edit",
                                                                 'event' => $db->events[$id])));
    }
});

$app->post('/edit', function () use ($app, $db, $staticText) {
    $id = $app->request->post('Id');
    if ( $id > 0 ) {
        $app->log->info("Editing [".$id."]");
        $data = array("Name" => $app->request->post('Name'),
                      "Location" => $app->request->post('Location'),
                      "StartDate" => $app->request->post('StartDate'),
                      "EndDate" => $app->request->post('EndDate'),
                      "Description" => $app->request->post('Description'),
                      "Id" => $id);
        $row = $db->events[$id];
        $row->update($data);
        $app->render('status.html', array_merge($staticText, array( 'Title' => "Eintrag bearbeiten",
                                                                    'Status' => "Änderungen gespeichert" )));
    }
});

// Run app
$app->run();
