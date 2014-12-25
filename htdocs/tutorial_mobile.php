<?php
require_once 'castle_engine_functions.php';
tutorial_header('Developing mobile games');

$toc = new TableOfContents(
  array(
    new TocItem('Compiling and debugging', 'compiling_debuggng'),
    new TocItem('Create a platform-independent Game unit', 'portable_code'),
    new TocItem('Prepare for differences in input handling', 'input'),
  )
);
?>


<p>Mobile platforms (Android, iOS) have some significant differences
from desktop platforms (Windows, Linux, Mac OS X, FreeBSD...).
While our engine hides them from you as much as possible, there are some facts
you just have to know.
<!--
Now that you've seen how to create a simple game, with level and player,
it seems a good moment to talk about them.
-->

<?php echo $toc->html_toc(); ?>
<?php echo $toc->html_section(); ?>

<p>Developing for mobile platforms requires installing
some special tools. Everything is explained on these platform-specific pages:

<ul>
  <li><a href="http://sourceforge.net/p/castle-engine/wiki/Android%20development/">Developing for Android</a>
  <li><a href="http://sourceforge.net/p/castle-engine/wiki/iOS%20Development/">Developing for iOS (iPhone, iPad)</a>
</ul>

<p>Compiling and packaging cross-platform games is greatly
simplified if you use our <a href="https://sourceforge.net/p/castle-engine/wiki/Build%20tool/">build tool</a>.
In particular for Android, our build tool nicely hides from you
a lot of complexity with managing an Android project.
So be sure to give it a try!

<p>Rendering on mobile platforms uses OpenGLES. Our OpenGLES renderer
can handle almost everything the same as a desktop OpenGL renderer,
but <a href="https://sourceforge.net/p/castle-engine/wiki/OpengLES,%20Android%20and%20iOS%20TODOs/">there
are some things not implemented yet in the OpenGLES version</a>.

<?php echo $toc->html_section(); ?>

<p>It is easy to create a game that can be compiled <i>both</i> for
Android/iOS and for desktop. To make it work, you should create a simple
platform-independent main game unit (traditionally called just <tt>game.pas</tt>
in our example projects) and maintain a very small platform-specific
game program/library files.

<p>Examples of this approach can be found
in most new projects/examples of our engine. For example see
<tt>castle_game_engine/examples/2d_spine_game/</tt> code.
Or <a href="http://castle-engine.sourceforge.net/darkest_before_dawn.php">Darkest Before the Dawn</a>
source code (see <a href="http://svn.code.sf.net/p/castle-engine/code/trunk/darkest_before_dawn/code/game.pas">game.pas of Darkest Before the Dawn</a>).

<p>The idea is to:

<ol>
  <li><p>Define main (platform-independent) game code in a unit like
    <tt>game.pas</tt> (and of course use other units, as needed).

  <li><p>The initialization section of the <tt>Game</tt> unit should only
   assign callbacks to <?php api_link('Application.OnInitialize', 'CastleWindow.TCastleApplication.html#OnInitialize'); ?>,
   and create window and assign it's callbacks.
   Do not do any real processing in the unit initialization,
   e.g. do not load any game data (because this is too early,
   for example Android activity is not started at this point yet).

   <p>Actual game initialization (loading images, resources, setting up player
   and such) should happen in the callback implementing <?php api_link('Application.OnInitialize', 'CastleWindow.TCastleApplication.html#OnInitialize'); ?>,
   and the game initialization that requires OpenGL context
   (like <?php api_link('SceneManager.LoadLevel', 'CastleLevels.TGameSceneManager.html#LoadLevel'); ?>)
   should happen in <?php api_link('Window.OnOpen', 'CastleWindow.TCastleWindowCustom.html#OnOpen'); ?>
   callback.

   <p>The unit should expose the <tt>Window</tt> instance,
   that will be used by platform-specific program/library code.
   It should descend from <?php api_link('TCastleWindowCustom', 'CastleWindow.TCastleWindowCustom.html'); ?> class
   (in most cases, just use the standard <?php api_link('TCastleWindowTouch', 'CastleWindowTouch.TCastleWindowTouch.html'); ?> class,
   although you can also derive your own window classes).

<?php echo pascal_highlight(
'{$mode objfpc}{$H+}

{ Implements the game logic, independent from mobile / standalone. }
unit Game;

interface

uses CastleWindowTouch, ...;

var
  Window: TCastleWindowTouch;

implementation

uses SysUtils, CastleWindow, CastleScene, CastleControls, ...;

var
  SampleImage: TCastleImageControl;
  SampleScene: TCastleScene;

{ routines ------------------------------------------------------------------- }

{ One-time initialization of resources, independent of OpenGL context. }
procedure ApplicationInitialize;
begin
  Image := TCastleImageControl.Create(Window);
  Image.URL := ApplicationData(\'sample_texture.png\');
  Window.Controls.InsertFront(Image);

  Scene := TCastleScene.Create(Application);
  Scene.Load(\'my_scene.x3d\');
  Scene.Spatial := [ssRendering, ssDynamicCollisions];
  Scene.ProcessEvents := true;
  Window.SceneManager.Items.Add(Scene);
  Window.SceneManager.MainScene := Scene;
end;

procedure WindowOpen(Container: TUIContainer);
begin
  { OpenGL context is created, initialize things that require OpenGL
    context. Often you do not need to use this callback (engine components will
    automatically create/release OpenGL resource when necessary),
    but sometimes it may be handy (e.g. SceneManager.LoadLevel must be
    done when OpenGL context is active, it will also show a progress bar
    on OpenGL context).

    You could also always derive new classes from TUIControl or it\'s descendants,
    and override their GLContextOpen / GLContextClose methods to react to
    context being open/closed.

    WindowOpen is always *after* ApplicationInitialize.
    In normal circumstances, for a standalone game, the WindowOpen will
    happen only once. But for other targets, it may be necessary to close/reopen
    the OpenGL context many times, e.g. on mobile platforms it\'s normal
    that application may "loose" the OpenGL context and it may need
    to recreate OpenGL resources when it wakes up. }
end;

procedure WindowResize(Container: TUIContainer);
begin
  // ... react to Container Width / Height changes
end;

procedure WindowUpdate(Container: TUIContainer);
begin
  // ... do something every frame
end;

procedure WindowPress(Container: TUIContainer; const Event: TInputPressRelease);
begin
  // ... react to press of key, mouse, touch
end;

function MyGetApplicationName: string;
begin
  Result := \'my_fantastic_game\';
end;

initialization
  { This should be done as early as possible to mark our log lines correctly. }
  OnGetApplicationName := @MyGetApplicationName;

  { initialize Application callbacks }
  Application.OnInitialize := @ApplicationInitialize;

  { create Window and initialize Window callbacks }
  Window := TCastleWindowTouch.Create(Application);
  Application.MainWindow := Window;
  Window.OnResize := @WindowResize;
  Window.OnUpdate := @WindowUpdate;
  Window.OnPress := @WindowPress;
end.'); ?>

  <li><p>In the <tt>Game</tt> unit, <b>do not</b>:

    <ul>
      <li>Do not call <tt>Window.Open</tt> or <tt>Window.Close</tt> or
        <tt>Application.Run</tt>.

      <li><p>Do not create more than one <tt>Window</tt>.
        If you want your game to be truly portable to <b>any</b> device &mdash;
        you have to limit yourself to using only one <tt>Window</tt>.
        For normal games that's probably natural anyway.

        <p>Note that the engine still supports, and will always support,
        multiple-window programs, but for that you will have to just write
        your own program code. See e.g.
        <tt>castle_game_engine/examples/window/multi_window.lpr</tt> example.
        There's no way to do it portably, for Android, iOS, web browser plugin...
    </ul>

  <li>Merely use the Game unit from .lpr files that are specific to platform.

    <p>Android .lpr file should define a library, and may be as simple as this:

<?php echo pascal_highlight(
'{$mode objfpc}{$H+}
library my_fantastic_game_android;
uses CastleAndroidNativeAppGlue, Game;
exports ANativeActivity_onCreate;
end.'); ?>

    <p>Desktop .lpr file should define a program that opens the window
    and runs the application. It may be as simple as this:

<?php echo pascal_highlight(
'{$mode objfpc}{$H+}
program my_fantastic_game_standalone;
uses CastleWindow, CastleConfig, Game;
begin
  Application.Initialize;
  Window.OpenAndRun;
end.'); ?>

    <p>Desktop .lpr file can do some more useful stuff,
    for example initialize window to be fullscreen, read command-line parameters,
    and load/save user configuration. See examples how to do it:
    <a href="http://svn.code.sf.net/p/castle-engine/code/trunk/darkest_before_dawn/code/darkest_before_dawn_standalone.lpr">darkest_before_dawn program file (simple)</a>
    or <a href="http://svn.code.sf.net/p/castle-engine/code/trunk/hotel_nuclear/code/hotel_nuclear.lpr">hotel_nuclear (more complicated)</a>.
</ol>

<?php echo $toc->html_section(); ?>

<p>To create portable games you have to think about different types
of inputs available on mobile platforms vs desktop.
The engine gives you various helpers, and abstracts various things
(for example, mouse clicks and touches can be handled using the same API,
you just don't see multi-touches on desktop).
But it's not possible to hide the differences 100%,
because some concepts just cannot work &mdash; e.g. mouse look cannot work
on touch interfaces (since we don't get motion events when you don't press...),
keyboard is uncomfortable on touch devices,
multi-touch doesn't work on desktops and so on.

<p>A simple example of a 3D navigation, if you use <tt>Player</tt> instance
(see previous tutorial chapter for overview how to use it):</p>

<?php echo pascal_highlight(
'{ 1. Declare a trivial global variable that controls whether
  input is touch (with multi-touch, without keyboard etc.) or desktop
  (without multi-touch, with keyboard etc.).

  Using a variable to toggle desktop/touch input,
  that can even be changed at runtime, is useful in Michalis experience
  --- it allows to somewhat debug touch input on desktops,
  by just setting DesktopCamera to false, e.g. when a command-line option
  like --touch-device is given. }

var
  DesktopCamera: boolean =
    {$ifdef ANDROID} false {$else}
    {$ifdef iOS}     false {$else}
                     true {$endif} {$endif};

{ 2. Then, somewhere where you initialize the game
     (probably after SceneManager.LoadLevel?) initialize the input.

     The code below assumes that you initialized Player, and you assigned
     SceneManager.Player := Player. This way we can be sure that current
     camera (SceneManager.Camera) is equal to player
     (SceneManager.Camera = Player.Camera). }

if DesktopCamera then
begin
  Player.Camera.MouseLook := true;
end else
begin
  Window.AutomaticTouchInterface := true;
  { Above will automatically set Window.TouchInterface based on
    current navigation mode (walk, fly..).
    It will also set camera MouseDragMode, which is very useful
    in combination with some touch interface modes.
    To actually enable the dragging, you still need to do this: }
  Player.EnableCameraDragging := true;
end;'); ?>

<?php
tutorial_footer();
?>
