<?php
require_once 'castle_engine_functions.php';
manual_header('Loading, displaying a scene (simple loading of 3D models)');

$toc = new TableOfContents(
  array(
    new TocItem('Get sample 3D model', 'model'),
    new TocItem('Write the code!', 'code'),
    new TocItem('Set the camera', 'camera'),
    new TocItem('Explanation: What is a "Scene Manager"', 'create'),
    new TocItem('Try some impressive 3D models', 'demo_models'),
  )
);

echo castle_thumbs(array(
  array('filename' => 'cars_demo_0.png', 'titlealt' => 'Car 3D model'),
));
?>

<p>We will now load a 3D model from file using the <code>TCastleScene</code> class,
and display it.</p>

<?php echo $toc->html_toc(); ?>

<?php echo $toc->html_section(); ?>

<p>Note that a 3D model doesn't have to be static.
It can include animated stuff, interactions,
3D sounds, scripts, and more.
<?php echo a_href_page('Our scene graph based on X3D is really quite powerful', 'vrml_x3d'); ?>.

<p>A sample 3D model may be found inside the engine examples,
in <code>examples/3d_rendering_processing/data/car.x3d</code> file.
My advice is to copy now the whole directory <code>data</code> from there into your
own project directory (so you have files like <code>data/car.x3d</code>).
The name <code>data</code> is special, it cooperates with the default
behavior of our <?php api_link('ApplicationData', 'CastleFilesUtils.html#ApplicationData'); ?>
 function.

<p>You can also download
<?php echo a_href_page('our demo VRML/X3D models', 'demo_models'); ?>.
And you can open all the models with
<?php echo a_href_page('view3dscene', 'view3dscene'); ?>,
to see how do they look like before loading them into your game.</p>

<p>And if you want to make your own 3D model, go ahead &mdash; generally use any
3D modeler and export to any 3D format we can handle, preferably X3D.
See <?php echo a_href_page('our guide to creating game data', 'creating_data_intro'); ?> for more information about preparing 3D data.</p>

<?php echo $toc->html_section(); ?>

<ol>
  <li><p><b>If you use Lazarus form with
    <?php api_link('TCastleControl', 'CastleControl.TCastleControl.html'); ?>:</b>
    To load a 3D model, double click to create an event <code>OnCreate</code>
    on your <code>TForm1</code> class, and put there the following code:</p>

<?php echo pascal_highlight(
'// also add to your uses clauses these units: CastleSceneCore, CastleScene;

procedure TForm1.FormCreate(Sender: TObject);
var
  Scene: TCastleScene;
begin
  Scene := TCastleScene.Create(Application);
  Scene.Load(\'car.x3d\');
  Scene.Spatial := [ssRendering, ssDynamicCollisions];
  Scene.ProcessEvents := true;

  CastleControl1.SceneManager.Items.Add(Scene);
  CastleControl1.SceneManager.MainScene := Scene;
end;'); ?>

  <li><p><b>If you use
    <?php api_link('TCastleWindow', 'CastleWindow.TCastleWindow.html'); ?></b>:
    To load a 3D model, change your program code to this:</p>

    <?php echo pascal_highlight_file('code-samples/view_3d_model_basic.lpr'); ?>
</ol>

<p>At the beginning we create a new instance of
<?php api_link('TCastleScene', 'CastleScene.TCastleScene.html'); ?>, and load
it's contents from a file.
<?php api_link('Scene.Spatial', 'CastleSceneCore.TCastleSceneCore.html#Spatial'); ?>
 determines what spatial
structures (octrees for now) are created, the value <code>[ssRendering,
ssDynamicCollisions]</code> is the most flexible one (it allows to speed up
the rendering by frustum culling, detect collisions between player and
level, and it adapts to a dynamic level that may have some animated
parts).
<?php api_link('Scene.ProcessEvents', 'CastleSceneCore.TCastleSceneCore.html#ProcessEvents'); ?>
 activates animating VRML/X3D models (you
can remove it if you know that your level is, and always will be, static).</p>

<p>The level is added to the scene manager. The level is also set as the
<?php api_link('MainScene', 'CastleSceneManager.TCastleSceneManager.html#MainScene'); ?>
 of scene manager, this means that some central settings
(like initial camera position, initial headlight status and such) can
be obtained from this scene.</p>

<?php echo $toc->html_section(); ?>

<p>To control the initial camera view:

<ol>
  <li><p>One option is to <b>control the camera by code</b>:
    use the <?php api_link('SceneManager.RequiredCamera.SetView',
    'CastleCameras.TCamera.html#SetView'); ?> method.
    This takes three vectors &mdash; position, look direction and look up vector.
    Simply add the <?php api_link('CastleVectors',
    'CastleVectors.html'); ?> unit to your uses clause,
    and call this:

<?php echo pascal_highlight(
'Window.SceneManager.RequiredCamera.SetView(
  Vector3(-7.83,  6.15, -7.55),
  Vector3( 0.47, -0.30,  0.82),
  Vector3( 0.16,  0.95,  0.25)
);'); ?>

    <p>Instead of <code>SceneManager.RequiredCamera</code> above,
    you could use <code>SceneManager.WalkCamera</code> or
    <code>SceneManager.ExamineCamera</code>
    to force creating camera of the given type.

    <p>You have a lot of options to control the camera from code:
    <ul>
      <li><p>You can assign a specific camera descendant to
        <?php api_link('SceneManager.Camera',
        'CastleSceneManager.TCastleAbstractViewport.html#Camera'); ?>.
        If you don't, the camera instance is automatically created when you use
         <?php api_link('SceneManager.RequiredCamera',
        'CastleSceneManager.TCastleAbstractViewport.html#RequiredCamera'); ?>,
         <?php api_link('SceneManager.WalkCamera',
        'CastleSceneManager.TCastleAbstractViewport.html#WalkCamera'); ?>,
         <?php api_link('SceneManager.ExamineCamera',
        'CastleSceneManager.TCastleAbstractViewport.html#ExamineCamera'); ?>
         or anything that underneath requires a camera (e.g. rendering requires a camera).
      <li><p>You can change the navigation type by setting
        <?php api_link('SceneManager.NavigationType',
        'CastleSceneManager.TCastleAbstractViewport.html#NavigationType'); ?>.
      <li><p>You can change the <?php api_link('SceneManager.Camera.Input',
        'CastleCameras.TCamera.html#Input'); ?> to disable some default camera
        key and mouse operations. For example, you can call
        <code>Window.SceneManager.WalkCamera.Input := [];</code>
        to disable <i>any</i> way for user to automatically control the camera,
        useful if you want to move camera only by your own code.
    </ul>

  <li><p><b>You can also set camera defaults (including position/direction/up)
    in your data (X3D files), instead of hardcoding it in Pascal.</b>
    Use a <code>Viewpoint</code>
    or <code>OrthoViewpoint</code> node inside the
    <code>car.x3d</code>  file
    (in general, inside a scene that is set as <code>SceneManager.MainScene</code>)
    to define the initial camera view. You can generate this
    <code>Viewpoint</code> using the
    <?php echo a_href_page('view3dscene', 'view3dscene'); ?> feature
    <i>"Console -&gt; Print Current Camera (Viewpoint)"</i>,
    or by setting the camera in Blender before exporting this X3D file.

    <p>You can also control the initial camera type using the <code>NavigationInfo</code>
    X3D node. It determines e.g. whether we are in EXAMINE, WALK, FLY, NONE or other modes.

    <p>Note that, in order for the "bindable" nodes (like <code>Viewpoint</code>,
    <code>OrthoViewpoint</code>, <code>NavigationInfo</code>) to work,
    they must be within a scene set as
    <?php api_link('SceneManager.MainScene', 'CastleSceneManager.TCastleSceneManager.html#MainScene'); ?>.
    The first <code>Viewpoint</code> or <code>OrthoViewpoint</code>
    is automatically used, just like the first <code>NavigationInfo</code> node.
    You can always call explicitly <code>Viewpoint.EventSet_Bind.Send(true)</code>
    to activate (jump to) a specific Viewpoint node.

    <p>The camera settings (position, direction, up, navigation type, avatar height
    and many others) set in X3D file (by nodes like <code>Viewpoint</code>,
    or <code>OrthoViewpoint</code>,
    <code>NavigationInfo</code>) serve as defaults when we create a camera instance.
    You can override them all by code, as documented above.

  <li><p>Warning: The simple methods <code>TCastleWindow.Load</code>
    or <code>TCastleControl.Load</code> forcefully reset (free and recreate)
    the camera.
    These methods are mostly designed for the case when your 3D world
    is actually only one 3D scene (e.g. when you make a 3D model viewer).
    It's usually better to avoid these methods in more complex applications.
</ol>

<?php echo $toc->html_section(); ?>

<p><i>Scene manager</i> contains the whole knowledge about your game 3D world.
It is essential to add all your 3D stuff to a scene manager.
An instance of scene manager (class
<?php api_link('TCastleSceneManager', 'CastleSceneManager.TCastleSceneManager.html'); ?>)
is already created and available in the <code>SceneManager</code> property
of the <code>TCastleControl</code> or <code>TCastleWindow</code> instance.

<p>By default <?php api_link('TCastleSceneManager', 'CastleSceneManager.TCastleSceneManager.html'); ?>
 also acts as a viewport filling the whole window. So the whole window shows
 your 3D world. In more complex scenarios you can have
many smaller viewports inside your window using <?php api_link('TCastleViewport', 'CastleSceneManager.TCastleViewport.html'); ?>.
You can also turn off scene manager from being a viewport
(setting <?php api_link('TCastleSceneManager.DefaultViewport', 'CastleSceneManager.TCastleSceneManager.html#DefaultViewport'); ?>
 to <code>false</code>), and then scene manager is really
<b>only</b> something that keeps track of 3D world, and nothing more.</p>

<p>For examples of using viewports, see:
<ul>
  <li>The <a href="manual_2d_user_interface.php">chapter about user interface</a> shows how to set additional viewport.</li>
  <li>Engine example <code>examples/3d_rendering_processing/multiple_viewports.lpr</code></li>
  <li>Engine example <code>examples/fps_game/fps_game.lpr</code></li>
</ul>

<!--See also <a
href="https://castle-engine.io/vrml_engine_doc/output/xsl/html/section.custom_viewports.html">custom
viewports notes</a, and ).-->

<p>In more advanced
scenarios you may need to create and manage scene manager yourself.
There are special classes available (<code>TCastleControlCustom</code> or
<code>TCastleWindowCustom</code>) that allow you to use your own scene manager
instance (or maybe zero, or maybe more than one scene manager instance,
maybe a custom scene manager class...).
</p>

<?php echo $toc->html_section(); ?>

<p>An important strength of our engine is that you can express a lot
of stuff inside your data, that is inside
<?php echo a_href_page('VRML/X3D', 'vrml_x3d'); ?> models.
So many features of our engine
(<?php echo a_href_page('shaders','x3d_implementation_shaders'); ?>,
 <?php echo a_href_page('screen effects', 'x3d_extensions_screen_effects'); ?>,
 <?php echo a_href_page('mirrors', 'x3d_implementation_cubemaptexturing'); ?>
 and many many more) don't have any special Object Pascal examples,
because they are simply not needed. For simple uses, you just define what you
need inside VRML/X3D file (of course, for advanced usage you can do a lot more
with Object Pascal code, and you can always build/modify VRML/X3D graph
by Object Pascal code).
So <b>be sure to grab <?php echo a_href_page('our demo VRML/X3D models', 'demo_models'); ?></b>
 and try opening them with any engine example program (like the one you just created,
or even our <?php echo a_href_page('view3dscene', 'view3dscene'); ?>)
&mdash; you will find that everything just works,
not requiring a single line of Object Pascal code.</p>

<p><b>Our model viewer <?php echo a_href_page('view3dscene', 'view3dscene'); ?></b>
 allows to test your models before loading them to your games.

<?php
manual_footer();
?>
