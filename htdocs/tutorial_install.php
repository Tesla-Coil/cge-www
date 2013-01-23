<?php
  require_once 'tutorial_common.php';
  tutorial_header('Download and install the engine, try the demos');
?>

<p>If you haven't done it yet, <?php echo a_href_page('download the
engine source code with examples', 'engine'); ?>.</p>

<p>The example program that shows most of the features presented in
this tutorial is inside <tt>examples/fps_game/</tt> .
I suggest looking at it's source
code for a complete implementation that uses all the code snippets
shown in this tutorial.</p>

<ul>
  <li><p><b>If you want to develop using <a href="http://www.lazarus.freepascal.org/">Lazarus</a>:</b>
    Run Lazarus,
    and open packages in the <tt>castle_game_engine/packages/</tt>
    subdirectory. Open and compile all three packages (<tt>castle_base.lpk</tt>,
    <tt>castle_components.lpk</tt>, <tt>castle_window.lpk</tt>), to test that things compile
    OK. Then install the package <tt>castle_components</tt>. (Do not install
    <tt>castle_window</tt> &mdash; you don't want to have this installed in
    Lazarus. You can install <tt>castle_base</tt> explicitly, but there's no need
    to: installing <tt>castle_components</tt> will also install <tt>castle_base</tt>
    automatically.)</p>

    <p>Once packages are successfully installed, Lazarus restarts, and you
    should see <i>"Castle"</i> tab of components at the top (TODO: screenshot). Sorry,
    we don't have icons for our components yet, so it looks a little
    boring. Mouse over the icons to see component names.</p>
  </li>

  <li><p><b>Alternatively, our engine can be used without Lazarus and LCL
    (Lazarus Component Library)</b>. Many of the example programs
    use our own <tt>CastleWindow</tt> unit to communicate with window manager,
    and they do not depend on Lazarus LCL.
    You can use command-line <tt>xxx_compile.sh</tt> scripts (or just call
    <tt>make examples</tt>) to compile them using FPC.

    <p>You will not be able to compile components and examples using LCL
    of course (things inside <tt>src/components/</tt> and <tt>examples/lazarus/</tt>).
  </li>
</ul>

<p>Let's quickly open and run some demos, to make sure that everything
works. I suggest running at least
<tt>examples/lazarus/model_3d_viewer/</tt> (demo using Lazarus LCL) and
<tt>examples/fps_game/</tt> (demo using our CastleWindow unit).</p>

<p>Make sure you have installed the necessary libraries first, or some of
the demos will not work. The required libraries (.so under Unix, .dll
under Windows) are mentioned in the <a
href="http://castle-engine.sourceforge.net/apidoc/html/introduction.html#SectionLibraries">Requirements
-&gt; Libraries</a> section of our reference introduction. Under
Windows, you will usually want to grab <a href="http://castle-engine.sourceforge.net/miscella/win32_dlls.zip">Windows DLLs</a>  and place
them somewhere on your $PATH, or just place them in every directory
with .exe files that you compile with our engine.</p>

<p>Now we'll start creating our own game from scratch.</p>

<?php
  tutorial_footer();
?>