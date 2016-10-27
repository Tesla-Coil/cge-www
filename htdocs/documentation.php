<?php
  define('CASTLE_GITHUB_NAME', 'castle-engine');

  require_once 'castle_engine_functions.php';
  castle_header("Castle Game Engine - Documentation for developers", NULL, array('documentation'));

  $toc = new TableOfContents(
    array(
      new TocItem('Getting started', 'getting_started'),
      new TocItem('Documentation', 'docs'),
      new TocItem('Other useful downloads', 'other_downloads'),
      new TocItem('FPC (Free Pascal Compiler) version required', 'fpc_ver'),
      new TocItem('Get the latest sources from GIT or SVN', 'bleeding_edge'),
      new TocItem('License', 'license'),
    )
  );

/*  echo flattr_button(); */

  echo '<img src="images/castle_game_engine_icon.png"
    alt="Castle Game Engine icon"
    style="float: right; clear: right; margin-top: 1em;" />';

  echo pretty_heading('Castle Game Engine', VERSION_CASTLE_GAME_ENGINE, 'Documentation');
?>

<?php echo $toc->html_toc(); ?>

<?php echo $toc->html_section(); ?>

<ul>
  <li><p>Install the <a href="http://lazarus.freepascal.org/">Lazarus</a> IDE.
    Alternatively, install just the command-line <a href="http://freepascal.org/">Free Pascal Compiler</a>.

  <li><p><b>For <a href="http://lazarus.freepascal.org/">Lazarus</a> users</b>:
    <ol>
      <li>Open and <b>compile the package <code>castle_base.lpk</code></b>
        You will find it in the <code>castle_game_engine/packages/</code> subdirectory.
        Use the Lazarus menu item <i>"Package -&gt; Open Package File (.lpk)"</i>
        to open the package file, press <i>"Compile"</i> in dialog that appears.
      <li>Then open and <b>compile the package <code>castle_window.lpk</code></b>.
      <li>Finally, open and <b>install the package <code>castle_components.lpk</code></b>.
        In package window, the option to <i>"Install"</i> package is under the <i>"Use"</i> button.
    </ol>

    <p>Then just compile and run from Lazarus any example you want
    (in <code>examples</code> subdirectory), just open the <code>xxx.lpi</code>
    project file and compile/run as any other program.

  <li><p><b>For command-line FPC users</b>:
    First, compile the examples <!-- (the ones that do not require Lazarus) -->
    by simple "<code>make examples</code>".
    Or execute <code>xxx_compile.sh</code> scripts to compile particular
    examples.

    <p>If you don't want to use Lazarus to compile your own programs,
    you have a couple of options.
    <ol>
      <li>First of all, we advice using our
        <a href="https://github.com/castle-engine/castle-engine/wiki/Build-Tool">build tool</a>
        to compile and package your games. The build tool reads the project
        configuration from the <code>CastleEngineManifest.xml</code> file.
        <!-- First compile the build tool itself (<code>./tools/build-tool/castle-engine_compile.sh</code>), -->
        <!-- move  -->
      <li>Other option is to compile the engine
        units by executing <code>make</code> inside the
        <code>castle_game_engine/</code> directory
        (uses <a href="http://wiki.freepascal.org/FPMake">FpMake</a>).
        Then add the path with compiled units to your <code>fpc.cfg</code> file by
        adding a line like <code>-Fu.../castle_game_engine/units/x86_64-linux</code>
        (<?php echo FPC_CFG_DOCS; ?>).
      <li>Final option is to use the <code>castle-fpc.cfg</code>
        and create <code>xxx_compile.sh</code> scripts (see examples) to compile
        your programs like that.
    </ol>
</ul>

<!--
The explanations that actually the engine
main OpenGL initialization method is <b>not</b> the Lazarus TOpenGLControl
takes too much space.

<p>There are also some Lazarus packages and examples (e.g. to extend Lazarus
<code>TOpenGLControl</code> component), they have to be compiled from
within Lazarus. Although note that the engine doesn't require LCL
for anything.
these are not an
essential part of the engine for now.
The main way for
initializing OpenGL for games is by CastleWindow unit that doesn't depend on
any Lazarus units. -->

<p><b>Now jump into our
<?php echo a_href_page('tutorial', 'tutorial_intro') ?> and you will
have a working game in 5 minutes! :)</b>

<p><b>Remember to install required libraries</b>:
programs developed using our engine (engine examples and normal programs)
may use a couple of libraries.
<!--
The full list of libraries is at the "Requirements" section at the documentation
of each program, and the
<a href="<?php echo reference_link(); ?>">reference</a>
also lists the libraries in the introduction section.
-->
In short, you will most likely want to have <i>LibPng, ZLib, GtkGLExt,
OpenAL, FreeType, and VorbisFile</i>.</p>

<ul>
  <li><i>On Linux and FreeBSD</i> you should install these libraries
    using your favorite package manager.
    Remember to install <code>-dev</code> versions of these libraries too
    (if you're under Debian or similar distribution) to be able to compile
    programs that link to these libraries.

  <li><i>On Windows</i> download DLLs from <a href="http://castle-engine.sourceforge.net/miscella/win32_dlls.zip">here (32-bit libraries zipped)</a> or <a href="http://svn.code.sf.net/p/castle-engine/code/trunk/external_libraries/i386-win32/">here (directory with 32-bit libraries)</a> or <a href="http://svn.code.sf.net/p/castle-engine/code/trunk/external_libraries/x86_64-win64/">here (directory with 64-bit libraries)</a>. Place all DLLs in program's exe directory (or put them somewhere on $PATH).

    <p>Be sure to use the ones corresponding to your compiler &mdash; if you use FPC/Lazarus for 32-bits, then you make executable for 32-bits, and you should use DLLs for 32-bits. <i>Even if you work on 64-bit Windows.</i> If in doubt, just try the other ones:)

<?php
/*
  <li><i>On Windows</i> get the DLL files from the engine sources you just downloaded.
    They are in:
    <ul>
      <li>(32-bit) <code>castle_game_engine/tools/build-tool/data/external_libraries/i386-win32/</code> or
      <li>(64-bit) <code>castle_game_engine/tools/build-tool/data/external_libraries/x86_64-win64/</code> .
    </ul>
    Place these DLL files in program's exe directory (or put them somewhere on $PATH).
    <!--
    These DLLs are already included in binary archives of my programs,
    but are not included in source archives (since they are only useful for Windows).
    -->
*/
?>
  <li><i>On Mac OS X</i>: <?php echo a_href_page('Mac OS X requirements are listed here',
    'macosx_requirements'); ?>.
</ul>

<!--
In general, for all OSes, see section
 in the documentation of programs and make sure that
you have appropriate libraries installed on your system.
-->

<?php echo $toc->html_section(); ?>

<ul>
  <li><?php echo a_href_page('Tutorial', 'tutorial_intro') ?></li>
  <li><i>Alternative tutorial:</i>
    <a href="http://castle-engine.sourceforge.net/miscella/cge_tutorial_slides.pdf">the slides</a>
    and <a href="https://github.com/castle-engine/cge-tutorial">their examples (sample data and code)</a>.
    This is a tutorial presented live by Michalis during
    the <i>Web3d&nbsp;2015 conference</i>.
    It shows (from the ground up) the creation of a simple 3D FPS game and 2D game.
    </li>
  <li><?php echo a_href_page('Classes overview (cheatsheet)', 'tutorial_classes_overview') ?></li>
  <li><?php echo a_href_page('Guide to creating game data', 'creating_data_intro') ?></li>
  <li><a href="<?php echo reference_link(); ?>">API reference</a>
  <li>Mobile development (Android, iOS): the <?php echo a_href_page('tutorial page about mobile development',
    'tutorial_mobile') ?> contains all the information</li>
</ul>

<p>The engine uses a cross-platform <b>Object Pascal language</b> and tools (<a href="http://freepascal.org/">FPC</a>, <a href="http://www.lazarus-ide.org/">Lazarus</a>). If you'd like to learn about the Object Pascal:

<ul>
  <li><p>We have a <a href="http://michalis.ii.uni.wroc.pl/~michalis/modern_pascal_introduction/modern_pascal_introduction.html">Quick Introduction to Modern Object Pascal</a> (<a href="http://michalis.ii.uni.wroc.pl/~michalis/modern_pascal_introduction/modern_pascal_introduction.pdf">PDF version</a>) (<a href="https://github.com/michaliskambi/modern-pascal-introduction">Sources on GitHub</a>). If you're a programmer, already familiar with common concepts like variables and classes, then this quick Pascal guide may be very useful to you:)
  <li><p><a href="http://www.freepascal.org/docs.var">FPC (Free Pascal Compiler) has extensive documentation</a>, including <i>Language reference guide</i> and a reference of RTL and FCL (standard library).
  <li><p>Many more resources are available on <a href="http://wiki.freepascal.org/">FPC and Lazarus Wiki</a>.
</ul>

<?php echo $toc->html_section(); ?>

<p><b>Our model viewer <?php echo a_href_page('view3dscene', 'view3dscene'); ?></b>
 allows to test your models before loading them to your games.

<p>An important strength of our engine is that you can express a lot
of stuff inside your data, that is inside
<?php echo a_href_page('VRML/X3D', 'vrml_x3d'); ?> models.
So many features of our engine
(<?php echo a_href_page('shaders','x3d_implementation_shaders'); ?>,
 <?php echo a_href_page('screen effects', 'x3d_extensions_screen_effects'); ?>,
 <?php echo a_href_page('mirrors', 'x3d_implementation_cubemaptexturing'); ?>
 and many many more) don't have any special ObjectPascal examples,
because they are simply not needed. For simple uses, you just define what you
need inside VRML/X3D file (of course, for advanced usage you can do a lot more
with ObjectPascal code, and you can always build/modify VRML/X3D graph
by ObjectPascal code).
So <b>be sure to grab <?php echo a_href_page('our demo VRML/X3D models', 'demo_models'); ?></b>
 and try opening them with any engine example program (like <code>scene_manager_basic.lpr</code>,
or even our <?php echo a_href_page('view3dscene', 'view3dscene'); ?>)
&mdash; you will find that everything just works,
not requiring a single line of ObjectPascal code.</p>

<?php echo $toc->html_section(); ?>

<p>You need the <a href="http://www.freepascal.org/">Free Pascal Compiler
(FPC)</a> to use our engine.
<b>We always support and advice the latest stable (from
<a href="http://www.freepascal.org/">freepascal.org</a>)
release of FPC (currently 2.6.4).</b>
Usually, we also support a couple of older FPC releases.
Currently (for engine 5.1.0) <b>FPC versions &gt;= 2.6.2 are supported</b>.

<p>You may also find it comfortable
to use <a href="http://lazarus.freepascal.org/">Lazarus</a>,
which is an IDE (editor, debugger etc.) built around FPC
with a visual classes library (LCL).
Our engine components can be used together with Lazarus forms
(although we also have an alternative window classes, independent from
Lazarus LCL).
<b>Any Lazarus version based on FPC &gt;= 2.6.2 is supported now.</b></p>

<!--p>I also regularly test FPC from SVN,
so it's usually painless to use even development FPC releases.</p-->

<!-- The policy for choosing FPC releases:
1. Advice and support latest FPC release from freepascal.org.
2. Try to support a couple of older FPC releases, as much as is reasonable.
The definition what is "reasonable" depends on:
1. What FPC version is inluded in distros (in particular, what is in
   last Debian stable and last Ubuntu LTS).
2. Do we need some new language features available in new FPC versions.
   For example, we heavily use FPC generics since some time, they make
   a lot of code very nice and clean, but they also bumped our FPC requirements
   at some point.
   We also use "nested of" construct of FPC 2.6.0, this again allows for
   much cleaner code, and also more optimized in one important case.
When our engine/view3dscene will be officially included in distros,
I would make the point 1. more influencial, and make everything possible
to hang on to FPC releases available in distros.
-->

<?php echo $toc->html_section(); ?>

<p>You can get the latest (<i>bleeding edge, work in progress!</i>)
engine sources from <a href="https://github.com/castle-engine/castle-engine">our GitHub project</a>.
For example run this:

<pre>git clone https://github.com/castle-engine/castle-engine.git</pre>

<p>Or you can get them from <a href="https://sourceforge.net/projects/castle-engine/">our SourceForge project</a>
SVN (auto-synchronized with GitHub GIT), like this:

<pre><?php echo sf_checkout_link(true, 'castle_game_engine'); ?></pre>

<?php echo $toc->html_section(); ?>

<p>The whole engine and all related programs' sources are licensed on terms of <a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License</a>. See <a href="http://www.gnu.org/">www.gnu.org</a> for more information about this license (including translations of it to various languages) and philosophy of free software.</p>

<p>Moreover, the core of the engine is also alternatively available under the more permissive <a href="http://www.gnu.org/copyleft/lesser.html">GNU Lesser General Public License</a> with the so-called "static linking exception". The idea of this exception is to allow statically linking with the engine on the same terms as dynamically linking. (<i>Static linking</i> is what normally happens when you compile a program using my units, without wrapping them in a DLL / Delphi runtime package.)</p>

<p>All this basically means that you have to share your modifications <i>to the engine</i>, and you can use the engine in closed-source programs.</p>

<p>The precise legal text of the "static linking exception" follows (it's the same as used by <a href="http://www.freepascal.org/faq.var#general-license">Free Pascal Runtime Library</a> and many other projects):</p>

<p style="margin-left: 2em; background: #EEE;">
As a special exception, the copyright holders of this library give you permission to link this library with independent modules to produce an executable, regardless of the license terms of these independent modules, and to copy and distribute the resulting executable under terms of your choice, provided that you also meet, for each linked independent module, the terms and conditions of the license of that module. An independent module is a module which is not derived from or based on this library. If you modify this library, you may extend this exception to your version of the library, but you are not obligated to do so. If you do not wish to do so, delete this exception statement from your version.
</p>

<p>Important notes:</p>
<ul>
  <li><p>If you want to use the engine on LGPL terms (as opposed to more strict GPL) you <i>must</i> compile the engine with <code>CASTLE_ENGINE_LGPL</code> symbol defined in file <code>castle_game_engine/base/castleconf.inc</code>. Just put there <code>{$define CASTLE_ENGINE_LGPL}</code> line (or simply remove the beginning space in already prepared comment <code>{&nbsp;$define CASTLE_ENGINE_LGPL}</code>).</p>

    <p>This is necessary to avoid pulling in GPL-only dependencies. For now, this is only the NURBS unit (uses GPL-only code from <a href="http://wdune.ourproject.org/">White_dune</a>). This missing NURBS implementation is the only difference between LGPL and "strict GPL" engine version.</p></li>

  <li><p>Note that LGPL stuff concerns only the engine, i.e. things inside <code>castle_game_engine</code> archive. The rest of the programs (<code>view3dscene</code>, <code>castle</code> etc.) are still strict GPL.</p></li>
</ul>

<?php
  castle_footer();
?>