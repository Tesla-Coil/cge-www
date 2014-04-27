<?php

/* Next news:
font_from_image_screen_0.png
compare_anti_aliasing.png
font_from_texture.png
font_from_image_screen_0.png
android_cubemap_1
android_progress_bar
android_message

darkest_before_dawn_ui.png

little_things_screen_0.png
little_things_screen_10.png
little_things_screen_2.png
little_things_screen_4.png
little_things_screen_5.png
little_things_screen_7.png
little_things_screen_8.png


<li>We have a new font loading and rendering method :)

We now use the FreeType library (and FreeType FPC unit) for loading fonts, which allows us to load at runtime a ttf font, and use it with any size, and with or without anti-aliasing. For rendering, we convert this font into a texture, and we render text by rendering a quad for each letter. This makes the font rendering modern (no display lists, just a single texture), and working with GLSL and OpenGLES20 (Android, iOS), and suitable both for anti-aliased and non-aliased text (resulting in alpha blending or alpha testing).

It is also possible to convert ttf font to a Pascal code, to easily embed the fonts inside Pascal program, and avoid the need for FreeType library at runtime (which also avoids the needs to worry about linking with FreeType). The program do it is texturefont2pascal (see castle_game_engine/examples/fonts/).

Important font classes are called now TTextureFont and (abstract) TCastleFont. TTextureFont is either loaded from ttf or from prepared data (when TTextureFontData was converted to a Pascal code). There is also new TSimpleTextureFont to draw a colorful text from glyphs in an image, like the ones by http://opengameart.org/users/asalga .

For a simplest example, to change the standard UIFont (used by default by various 2D controls) to a custom ttf font (file "MyFontFile.ttf" within your game data) you would do this:

  UIFont := TTextureFont.Create(ApplicationData('MyFontFile.ttf'), { size } 20, { antialiasing? } true);

Check out new example castle_game_engine/examples/fonts/font_from_texture.lpr  :)

Also, font lifetime is no longer limited to the OpenGL context. This means that you can create and use TCastleFont or TTextureFont instances more freely, e.g. create them once at the application initialization (no need to repeat it in every OnOpen), and measure text (e.g. calling TextWidth) at any time.

<li>We also have new property TGLImage.Color, to easily color the rendered images. Useful for fonts, but also useful for general image rendering.﻿
<li>New example examples/fonts/font_from_image.lpr showing how to use a font painted as an image.
<li>The <a href=Send method of X3D events</a> is now safer to use. Now all EventXxx properties have a specialized type, like TSFBoolEvent or TSFStringEvent, and you can only call Send with proper parameters. <!-- (Previously all events were of TX3DEvent class, and Send() was overloaded for all types. This made mistakes in values possible to detect only at runtime, by catching EInvalidCast errors. Now they are catched at compile time.) -->
<li>The ARCHITECTURE mode was renamed to TURNTABLE, following InstantReality mode that has a similar purpose.
<li>The DrawStyle, OnDrawStyle, Draw, OnDraw renamed to RenderStyle and Render. We made some effort to keep compatibility (e.g. the old TUIControl.Draw method still exists and can be overridden, new TUIControl.Render simply calls it) but you're adviced to convert to new names anyway. New names are cleaner and safer: there is no more a "dsNone" value, instead RenderStyle (both on TUIControl and TCastleWindow and TCastleControl) is now by default rs2D.
<li>Android supports now Application.ProcessMessages correctly. This means that you can use modal functions, that inside run the message loop and return only when some state finished --- for example, various modal boxes of CastleMessages like MessageOK and MessageYesNo. The android_demo contains a demo of it.
  Note that not all backends guarantee the support for Application.ProcessMessages. For now, the LIBRARY backend (used on iOS) doesn't support Application.ProcessMessages, so modal functions are not possible (instead, you have to implement your own state machine e.g. using controls like TCastleDialog underneath).
  A lot of other small improvements around Android backend happened.
<li>New class TUIContainer makes it easier to create and use containers (like the ones provided by TCastleWindow and TCastleControl). Various small fixes and improvements come as a result of that. You may need to adjust your window callbacks to take "Container: TUIContainer" parameter (although we added compatibility alias TCastleWindowBase = TUIContainer to make it possible to still compile old code; but remember that now TUIContainer and TCastleWindowCustom are totally separate classes). The only window classes now are TCastleWindowCustom and TCastleWindow, and the only control classes now are TCastleControlCustom and TCastleControl (the non-Custom versions have comfortable ready SceneManager).
  This also cleaner reflects that the basis for all engine rendering (2D and 3D) is now TUIControl. So all container providers (TCastleWindowCustom, TCastleControlCustom) give you Controls list with the same behaviour.
<li>OpenGLES (Android, iOS) renderer improvements:
  <ol>
    <li>Most of the texture generation modes work now. This includes the default generation of texture coordinates when they are not specified. (Which is equivalent to using TextureCoordinateGenerator with modes "BOUNDS2D", "BOUNDS3D", "BOUNDS", http://castle-engine.sourceforge.net/x3d_extensions.php#section_ext_tex_coord_bounds ). And it includes "COORD" (testcase: demo_models/texturing_advanced/tex_coord_generator_coord.x3dv ), "COORD-EYE", "SPHERE" (testcase: demo_models/texturing_advanced/tex_coord_generator_spherical_mapping.x3dv ).
      And it includes modes for cubemaps in camera-space (CAMERASPACENORMAL and CAMERASPACEREFLECTIONVECTOR) and world-space (many models in demo_models/cube_environment_mapping/ ). Generating cubemaps at runtime, for mirrors, was already working. So we can fully use cubemaps for mirrors on OpenGLES, just like on desktop OpenGL.
    <li>TextureTransform and friends (TextureTransform3D, TextureTransformMatrix3D) work on OpenGLES too.

------------------------------------------------------------------------------
g+ paste:

<li>(g+)
Screen effects, including Screen Space Ambient Occlusion, work on OpenGLES now too :)

- All screen effects predefined in view3dscene, including the ones using depth textures, work.
- ScreenSpaceAmbientOcclusion (built inside our SceneManager) works, although it's somewhat slow (but correct!) on my device.
- All custom screen effects, defined in VRML/X3D, work --- see demos in demo_models/screen_effects/ from http://castle-engine.sourceforge.net/demo_models.php .
- android_demo example (in castle_game_engine/examples/android/android_demo/ in SVN) contains a simple screen effect, to test that it works.

Also generating depth textures for other purposes, like in demo demo_models/rendered_texture/rendered_texture.x3dv , works on OpenGLES :)﻿

<li>(g+)
Screenshots from "Little Things", a tiny game done by Michalis during last weekend's TSG gamejam ( https://www.facebook.com/tsgcompo ). It was the best weekend ever, as it always happens during TSG compo! :)

The game was done using our Castle Game Engine ( http://castle-engine.sourceforge.net/ ). Of course open-source (code and data are available in SVN on http://svn.code.sf.net/p/castle-engine/code/trunk/little_things/ ). The binaries (for Windows, Linux) will be uploaded soon, for now you can just compile it yourself :)﻿

Hi :) Many news from last month's developments:

* Fog works on OpenGLES (Android, iOS) now. This includes simple linear/exponential fog using Fog node in VRML/X3D, and FogCoordinate, and volumetric fog (see http://castle-engine.sourceforge.net/x3d_extensions.php#section_ext_fog_volumetric) .

* * We have created an extensive documentation about developing for Android https://sourceforge.net/p/castle-engine/wiki/Android%20development/ and iOS https://sourceforge.net/p/castle-engine/wiki/iOS%20Development/ . They contain detailed instructions how to install everything necessary, how to compile, where are the examples... The Android docs contain also a section about "Debugging running application (on an Android device) using ndk-gdb" --- yes, it works flawlessly :) Finally, there's also a document summarizing currently missing features from OpenGLES renderer https://sourceforge.net/p/castle-engine/wiki/OpengLES,%20Android%20and%20iOS%20TODOs/ .

* Many improvements to the Android done, in particular everything is now ready for the fact that the we may loose OpenGL context at any time (as it can happen on Android). In new "Darkest Before the Dawn" http://castle-engine.sourceforge.net/darkest_before_dawn.php version you will be able to switch from/to the application at any time, even during the loading progress (so we can loose OpenGL context during preparing of OpenGL resources; they'll be prepared later on-demand in this case), and things will just work.

* Saving / restoring of the activity state is now done. We simply save/load the XML data from CastleConfig, so you just use Config.GetValue, Config.SetValue, Config.SetDeleteValue to save your state. Just like on standalone, but on Android we just load/save config automatically. See https://sourceforge.net/p/castle-engine/wiki/Android%20development/#saving-state .

* Multi-touch is implemented :)

Note: it does break compatibility. I initially implemented multi-touch keeping backwards compatibility, but then I decided to fix the issue with mouse/touch Y coordinate (99% of the engine considers "0 is bottom", mouse Y was considering "0 is top"; this inconsistency is now fixed, with mouse Y=0 meaning "bottom" everywhere). This means that compatibility break is unavoidable, otherwise we would have a real confusion which is which.

1. There is an example program in castle_game_engine/examples/android/drawing_toy/ where you can draw on the screen, with each finger index drawing with a different color. Try drawing with 2-5 fingers simultaneously :) It's pretty fun, and allows you to test the multi-touch support on Android :)

2. TCastleTouchControl now uses multi-touch. Cameras drag, and capture mechanism in TCastleUIContainer, were also updated to fully work with multi-touch.

3. API:
3.1. Press/Release:

TInputPressRelease structure (which is a parameter for OnPress/OnRelease events) has a new field FingerIndex. On devices with a single normal mouse (no touch), like on desktops, FingerIndex is always 0.

Notes:
- If hardware configurations that have both mouse and touch devices, or that have many mouse devices, will ever become popular, we can support them. Each mouse just reserves one FindexIndex value. (You can e.g. connect usb mouse to Android, so it actually already works on Android, although in that case Android hides it from us completely, mouse connected to Android usb simply simulates touch.)

- TInputPressRelease.MouseButton field is independent from TInputPressRelease.FingerIndex. For a single mouse, FingerIndex is always 0. For a touch, MouseButton is always mbLeft. We do not try to "merge" these 2 fields into one, as it would not be useful --- UIs do not generally react to pressing a second finger the same as pressing a right mouse button.

Internally (in castlewindow_library.inc) the DoMouseDown/Up and LibraryMouseDown/Up get now extra FingerIndex parameter.

3.2. Motions:

TUIControl has new method Motion, and TCastleWindow/TCastleControl have new callback OnMotion. They get a parameter TInputMotion, that contains new and old position, a FingerIndex, and Pressed describing which buttons are pressed (for mouse, this corresponds to actual buttons pressed, may be [] if none; for touch device, it is always [mbLeft]; this way you can just detect any dragging by Pressed <> []). Using a structure TInputMotion, and a generic name, will hopefully make it forward-compatible, like current OnPress and OnRelease.

The old MouseMove/OnMouseMove symbols are now removed. Since we change the API anyway (Y goes up, position is floats...), keeping compatibility for them was too much hassle.

3.3. Current state:

TCastleWindow and TCastleControl have a list of Touches, listing current state of the touches. It's length varies (can be zero if not touching). Right now, the state of touch is just it's position in 2D and FingerIndex, but we may extend it in the future. See comments at CastleUIControls.TTouch type.

You can use MousePosition to get position of touch with FingerIndex = 0, if any (will return zeros if no such touch). Consistently, the state of mbLeft in MousePressed says whether the touch with FingerIndex = 0 currently exists. The effect is that code that looks only at MousePosition/MousePressed will somewhat work with touch devices, treating them as a device with a mouse with a single (mbLeft) button (that mysteriously doesn't report move events when button is not pressed).

Oh, and the touch positions are floats, not ints, to support devices with sub-pixel precision.

Oh, and the touch position has Y going from bottom to top, just like our 2D drawing routines. No more the need to invert MouseY.﻿

*/

array_push($news,
    array('title' => 'Development: Android and iOS, new game release "Darkest Before the Dawn", more',
          'year' => 2014,
          'month' => 1,
          'day' => 2,
          'short_description' => '',
          'guid' => '2014-01-02',
          'description' =>
castle_thumbs(array(
  array('filename' => 'darkest_before_dawn_1.png', 'titlealt' => 'Screenshot from &quot;Darkest Before the Dawn&quot;'),
  array('filename' => 'darkest_before_dawn_2.png', 'titlealt' => 'Screenshot from &quot;Darkest Before the Dawn&quot;'),
  array('filename' => 'gles-2.png', 'titlealt' => 'One of the first 2D programs to run with OpenGLES renderer - &quot;isometric_game&quot; from engine examples'),
  array('filename' => 'cge-android-demo-1.png', 'titlealt' => 'One of the first 3D programs to run with OpenGLES renderer - &quot;android_demo&quot; from engine examples'),
  array('filename' => 'navigation_controls.png', 'titlealt' => 'Summary of new view3dscene AWSD controls'),
  array('filename' => 'new_walk_shortcuts.png', 'titlealt' => 'Tooltip with summary of new view3dscene AWSD controls'),
)) .
'<p>Hello everyone in 2014 :)

<p>First of all, I would like to present a small game I did
during a weekend "gamejam" at the end of November 2013:

<h3><a href="darkest_before_dawn.php">Darkest Before the Dawn</a></h3>

<p>The game is free to download for Android, Linux or Windows.
Of course the game uses our <a href="' . CURRENT_URL . 'engine.php">Castle
Game Engine</a> for everything. The complete game code and data are
available in our SVN repository.

<p>Next, we have news about the engine development:

<ol>
  <li>Yes, you have heard right: <b>the engine supports Android and iOS (iPhone, iPad)</b>.

    <p>The rendering is done using an OpenGL ES 2.0 renderer. Most of the rendering features work (and the rest is in-progress :). Rendering 3D shapes with shaders, textures, lighting of course works. Note that mobile shaders use the Gouraud shading for speed. Rendering 2D images (<a href="http://castle-engine.sourceforge.net/apidoc/html/CastleGLImages.TGLImage.html">TGLImage</a>) is also done. Since <a href="http://castle-engine.sourceforge.net/apidoc/html/CastleGLImages.TGLImage.html">TGLImage</a> is used underneath by all our 2D controls &mdash; most of 2D GUI works (except fonts, for now...).

    <p>You can compile the whole engine for OpenGLES (as opposed to normal desktop OpenGL) by defining symbol <tt>OpenGLES</tt> in <tt>base/castleconf.inc</tt> file. This symbol is defined automatically when we compile for Android or iOS, but you can also test OpenGLES renderer on normal systems like Windows and Linux (it\'s a nice way to test your mobile game on a desktop system).

    <p>We can initialize OpenGLES context using the EGL library. This is useful to initialize OpenGLES on Android or Xlib (with glX+EGL) or Windows (with wgl+EGL).

    <p>The Android port uses <a href="http://developer.android.com/reference/android/app/NativeActivity.html">NativeActivity</a>, available since Android 2.3. It requires FPC 2.7.1 to cross-compile code to Android ARM, see <a href="http://wiki.freepascal.org/Android">FPC wiki about Android</a>. Integration with Android includes using Android\'s log facility (just use <a href="' . CURRENT_URL . 'tutorial_log.php">WritelnLog from CastleLog</a> unit) and using Android\'s assets (URLs like <tt>assets:/my_texture.png</tt> are supported, and <a href="http://castle-engine.sourceforge.net/apidoc/html/CastleFilesUtils.html#ApplicationData">ApplicationData</a> returns <tt>assets:/</tt> to read data from apk).

    <p>You can develop cross-target games (for Android, iOS, or standalone &mdash; normal OSes like Linux or Windows) using the engine. This means that a single source code can be used to compile multiple versions of the game. We also have plans to make a "build tool" for the engine to auto-generate the necessary files for the compilation on given target (although it\'s unsure if this will be ready for next release). See <a href="https://sourceforge.net/p/castle-engine/wiki/Planned:%20build%20tool/">Planned: build tool</a> wiki page, and drop a comment if you\'re interested!

    <p>' . (!HTML_VALIDATION ? '<iframe width="560" height="315" src="//www.youtube.com/embed/8u7DggGe_Uk?rel=0" frameborder="0" allowfullscreen></iframe>' : '') . '

  <li><b>Engine can be compiled and used as a shared library</b> (<tt>castleengine.dll</tt> on Windows, <tt>libcastleengine.so</tt> on Linux), <b>with API accessible from C or C++</b>. We provide a C header in <tt>src/library/castlelib.h</tt> for this purpose. See code in <tt>src/library/</tt> and tests in <tt>examples/library/</tt> .

  <li><b>The default <a href="http://castle-engine.sourceforge.net/apidoc/html/CastleCameras.TWalkCamera.html">TWalkCamera</a> inputs are now equal to <a href="http://castle-engine.sourceforge.net/apidoc/html/CastlePlayer.TPlayer.html">TPlayer</a> inputs, in particular they honour common AWSD combo</b>.
    <ul>
      <li>You can move using AWSD by default (e.g. in <a href="http://castle-engine.sourceforge.net/view3dscene.php">view3dscene</a>).
      <li><i>Space / c</i> keys make <i>jump / crouch</i>. We no longer have separate inputs for jump / crouch (when gravity works) or flying up / down (when gravity doesn\'t work).
      <li>This avoids switching the meaning or left / right arrows in mouse look mode in view3dscene.
      <li>This makes keys in all our programs and games more consistent. And everyone knows AWSD, while previous shortcuts for strafing (comma and dot) were quite uncommon.
      <li>Of course, all TWalkCamera inputs remain configurable. So, if you really liked previous key shortcuts, you can restore them for your application.
    </ul>
  <li><b><a href="http://www.web3d.org/files/specifications/19775-1/V3.2/Part01/components/enveffects.html#TextureBackground">TextureBackground</a> support, making it possible to use <tt>MovieTexture</tt> as skybox sides</b>. The rendering of <tt>Background</tt> and <tt>TextureBackground</tt> uses new simplified code, that can utilize our texture cache and works on OpenGLES too.
  <li>Notes about <b><a href="' . CURRENT_URL . 'tutorial_transformation_hierarchy.php">transformation hierarchy</a></b> added to the documentation.
  <li>Context resource sharing (so that <b>many windows/controls work OK, sharing textures and fonts and such</b>) implemented for CastleWindow Xlib+GLX and GTK backends.
  <li>Support for <i>png</i> and <i>gz</i> formats without any external libraries (using FpRead/WritePng and PasZlib underneath). This is particularly useful for Android and iOS, where linking with external libraries is not so easy.
  <li>CastleEnumerateFiles API much changed and renamed to <a href="http://castle-engine.sourceforge.net/apidoc/html/CastleFindFiles.html">CastleFindFiles</a>. It supports searching for files inside Android assets too (although, unfortunately, not recursive &mdash; because of NDK API limitations).
  <li><tt>--hide-menu</tt> option implemented for view3dscene. Useful e.g. for fullscreen presentations, where you may want to hide all UI.
</ol>
')
);
