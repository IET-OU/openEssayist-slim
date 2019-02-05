<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * Define routes (moved from 'public_html/index.php')
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, 04-February-2019.
 */

function defineRoutes( $c )
{
  // Create the controllers
  $loginController = new \LoginController();
  $appController = new \HomeController();
  $adminCtrl = new \AdminController();
  $userCtrl = new \UserController();
  $demoCtrl = new \DemoController();
  $tutorCtrl = new \TutorController();
  $groupCtrl = new \GroupController();
  $uptimeCtrl = new \UptimeController();

  // Define the routes

  $c->app->get('/', array($appController, 'index'))->name('home');
  $c->app->get('/about', array($appController, 'about'))->name('about');
  $c->app->get('/privacy', array($appController, 'gdprPrivacyPolicy'))->name('GDPR.Privacy');

  // $c->app->get('/config', array($appController, 'testConfig'))->name('config');
  $c->app->get('/login', array($loginController, 'index'))->via('GET', 'POST')->name('login');
  $c->app->get('/logout', array($loginController, 'logout'))->name('logout');
  $c->app->get('/samslogin', array($loginController, 'samsLogin'))->via('GET', 'POST')->name('samslogin');

  $c->app->get('/me/', array($userCtrl, 'me'))->name('me.home');
  $c->app->get('/me/consent', array($loginController, 'consent'))->via('GET', 'POST')->name('consent');

  $c->app->get('/me/essay/(:id(/))', array($userCtrl, 'tasks'))->conditions(array('id' => '[0-9]+'))->name('me.tasks');

  $c->app->get('/me/essay/:idt/drafts/', array($userCtrl, 'manageDraft'))->name('me.draft.action');
  $c->app->get('/me/essay/:idt/history/', array($userCtrl, 'historyDraft'))->name('me.draft.history');
  $c->app->get('/me/essay/:idt/submit/', array($userCtrl, 'submitDraft'))->via('GET', 'POST')->name('me.draft.submit');

  $c->app->post('/api/process/:idt', array($userCtrl, 'processDraft'))->name('me.draft.process');
  $c->app->get('/api/draft/:draft/exhibit.json', array($userCtrl, 'getExhibitJSON'))->conditions(array('id' => '[0-9]+'))->name('api.draft.exhibit');
  $c->app->get('/api/orchestrator.json', array($tutorCtrl, 'getJSON'))->name('api.draft.orchestrator');

  $c->app->get('/me/draft/:draft/show/', array($userCtrl, 'showDraft'))->name('me.draft.show');
  $c->app->get('/me/draft/:draft/show/:cmd', array($userCtrl, 'showDraft'))->name('me.draft.showext')
  ->conditions(array('cmd' => '(text|keyword|sentence|all)'));
  $c->app->get('/me/draft/:draft/keyword/', array($userCtrl, 'showKeyword'))->name('me.draft.keywords');
  $c->app->get('/me/draft/:draft/stats/', array($userCtrl, 'showStats'))->name('me.draft.stats');
  $c->app->get('/me/draft/:draft/sentence/', array($userCtrl, 'showSentence'))->name('me.draft.sentence');

  $c->app->get('/me/draft/:draft/action/keyword', array($userCtrl, 'actionKeyword'))->name('me.draft.act.keyword');

  $c->app->get('/me/draft/:draft/group/texts', array($demoCtrl, 'groupTexts'))->name('me.draft.group.texts');
  $c->app->get('/me/draft/:draft/group/graphics', array($demoCtrl, 'groupGraphics'))->name('me.draft.group.graphics');
  $c->app->get('/me/draft/:draft/group/actions', array($demoCtrl, 'groupActions'))->name('me.draft.group.actions');

  // Visualizations.

  $c->app->get('/me/draft/:draft/view/network/ke', array($userCtrl, 'viewKeGraph'))->name('me.draft.view.kegraph');
  $c->app->get('/me/draft/:draft/view/network/se', array($userCtrl, 'viewSeGraph'))->name('me.draft.view.segraph');
  $c->app->get('/me/draft/:draft/view/cytoscape/se', array($userCtrl, 'viewCytoScape'))->name('me.draft.view.cytoscape');
  $c->app->get('/me/draft/:draft/view/links/se', array($userCtrl, 'viewLinksNetwork'))->name('me.draft.view.linksgraph');
  $c->app->get('/me/draft/:draft/view/vivagraph/se', array($userCtrl, 'viewVivaGraph'))->name('me.draft.view.vivagraph');
  $c->app->get('/me/draft/:draft/view/sigma/se', array($userCtrl, 'viewSigmaGraph'))->name('me.draft.view.sigma');
  $c->app->get('/me/draft/:draft/view/voronoi/se', array($userCtrl, 'viewVoronoiGraph'))->name('me.draft.view.voronoi');
  $c->app->get('/me/draft/:draft/view/hive/se', array($userCtrl, 'viewHivePlot'))->name('me.draft.view.hive');

  $c->app->get('/me/draft/:draft/view/dispersion', array($userCtrl, 'viewDispersion'))->name('me.draft.view.dispersion');
  $c->app->get('/me/draft/:draft/view/rainbow', array($userCtrl, 'viewRainbow'))->name('me.draft.view.rainbow');
  $c->app->get('/me/draft/:draft/view/structure', array($userCtrl, 'viewStructure'))->name('me.draft.view.structure');
  $c->app->get('/me/draft/:draft/view/target', array($userCtrl, 'viewTarget'))->name('me.draft.view.target');
  $c->app->get('/me/draft/:draft/view/matrix', array($userCtrl, 'viewMatrix'))->name('me.draft.view.matrix');
  $c->app->get('/me/draft/:draft/view/cloud', array($userCtrl, 'viewCloud'))->name('me.draft.view.cloud');
  $c->app->get('/me/draft/:draft/view/chord', array($userCtrl, 'viewChord'))->name('me.draft.view.chord');
  $c->app->get('/me/draft/:draft/view/exhibit', array($userCtrl, 'viewExhibit'))->name('me.draft.view.exhibit');
  $c->app->get('/me/draft/:draft/view/generator', array($userCtrl, 'viewGenerator'))->name('me.draft.view.generator');

  $c->app->post('/profile/data/keywords', array($userCtrl, 'saveKeywords'))->name('profile.save.keyword');
  $c->app->post('/profile/data/keywords/reset', array($userCtrl, 'resetKeywords'))->name('profile.reset.keyword');
  $c->app->get('/profile/data/notes', array($userCtrl, 'saveNotes'))->via('GET', 'POST')->name('profile.save.notes');

  $c->app->get('/ajax/draft/:draft/graph/:graph.json', array($userCtrl, 'ajaxGraph'))->name('ajax.graph.json')
  				->conditions(array('graph' => '(graphse|graphke)'));
  $c->app->get('/ajax/draft/:draft/keywords.json', array($userCtrl, 'ajaxKeyword'))->name('ajax.keyword.json');

  $c->app->post('/tutor/logactivity', array($tutorCtrl, 'postActivityLog'))->name('ajax.log.activity');

  $c->app->get('/me/report', array($tutorCtrl, 'getFeedback'))->via('GET', 'POST')->name('system.report');
  $c->app->get('/help/', array($tutorCtrl, 'getHelpSystem'))->name('system.help');
  $c->app->get('/help/on/:topic', array($tutorCtrl, 'getHelpOnTopic'))->name('ajax.help.topic');
  $c->app->get('/help/on/:topic/hints', array($tutorCtrl, 'getHelpOnTopic'))->name('ajax.help.hint');

  // ADMIN routes.

  $c->app->get('/admin/', array($adminCtrl, 'index'))->name('admin.home');
  $c->app->get('/admin/reset', array($adminCtrl, 'reset'))->name('admin.reset');
  $c->app->get('/admin/users', array($adminCtrl, 'allUsers'))->name('admin.users');
  $c->app->get('/admin/tasks', array($adminCtrl, 'allTasks'))->name('admin.tasks');
  $c->app->get('/admin/task/:taskid', array($adminCtrl, 'editTask'))->via('GET', 'POST')->name('admin.task.edit');
  $c->app->get('/admin/analyser', array($adminCtrl, 'showEssayData'))->name('admin.json');
  $c->app->get('/admin/history', array($adminCtrl, 'showHistory'))->name('admin.history');
  $c->app->get('/admin/feedback', array($adminCtrl, 'showFeedback'))->name('admin.feedback');
  $c->app->get('/admin/data/logs.js', array($adminCtrl, 'getLogs'))->name('admin.data.logs');
  $c->app->get('/admin/data/logs', array($adminCtrl, 'getLogsCSV'))->name('admin.data.csv');
  $c->app->get('/admin/data/logs(.:format)', array($adminCtrl, 'getLogsCSV'))->name('admin.data.log')
  	->conditions(array('format' => '(xlsx|json|tsv)'));
  $c->app->get('/admin/data/content', array($adminCtrl, 'getContentExcel'))->name('admin.data.content');
  $c->app->get('/admin/logs/table', array($adminCtrl, 'showLogsTable'))->name('admin.logs.table');
  $c->app->get('/admin/logs', array($adminCtrl, 'showLogs'))->name('admin.logs');
  $c->app->get('/admin/logs/:userid/', array($adminCtrl, 'showUserLogs'))->name('admin.logs.user');

  $c->app->get('/admin/group/:gid/addusers/(:nb(/:prf))', array($adminCtrl, 'addUsersToGroup'))->name('admin.task.adduser');
  $c->app->post('/admin/group/addusers', array($adminCtrl, 'addUsersToGroup'))->name('admin.task.postadduser');

  $c->app->get('/group/', array($groupCtrl, 'index'))->name('group.home');
  $c->app->get('/group/tasks/', array($groupCtrl, 'showTasks'))->name('group.task');
  $c->app->get('/group/task/:taskid', array($groupCtrl, 'editTask'))
  				->conditions(array('taskid' => '[0-9]+'))
  				->via('GET', 'POST')
  				->name('group.task.edit');
  $c->app->get('/group/task/new', array($groupCtrl, 'newTask'))->via('GET', 'POST')->name('group.task.new');

  $c->app->get('/status', [ $uptimeCtrl, 'status' ])->name('uptime.status');
  // Was:  $c->app->get('/uptime', [ $uptimeCtrl, 'backend' ])->name('uptime.backend');

  $c->app->error(array($appController, 'error'));
  $c->app->notFound(array($appController, 'NotFound'));

  return $c;
}
