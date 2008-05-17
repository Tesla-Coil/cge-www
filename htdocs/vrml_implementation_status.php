<?php
  require_once 'vrmlengine_functions.php';

  common_header("VRML implementation status", LANG_EN,
    NULL, NULL,
    '<style type="text/css"><!--
    td.pass{background-color:rgb(50%,100%,50%)}
    td.fail{background-color:rgb(100%,50%,50%)}
    td.invalid{background-color:rgb(75%,75%,75%)}
    --></style>
    ');

  $toc = new TableOfContents(
    array(
      new TocItem('VRML 2.0 nodes implemented', 'vrml_2_nodes'),
      new TocItem('X3D features in VRML 2.0', 'x3d', 1),
      new TocItem('VRML 1.0 status', 'vrml_1'),
      new TocItem('Tests passed', 'tests_passed'),
    ));
?>

<?php echo pretty_heading($page_title); ?>

<p>This page collects information about implementation status of
VRML constructs, with respect to VRML 1.0 and 2.0 specifications.
It also collects some details about handling of some nodes.
If you want to know what is supported <i>besides
the things required by VRML specifications</i>,
you should read the other page about
<?php echo a_href_page('VRML extensions implemented',
'kambi_vrml_extensions'); ?>.

<p><i>No limits</i>:
<a href="http://web3d.org/x3d/specifications/vrml/ISO-IEC-14772-VRML97/part1/conformance.html#7.3.3">
VRML 97 specification defines various limits</a>
that must be satisfied by VRML browsers to call themselves "conforming"
to VRML specification. For example, only 500 children per Group
have to be supported, only SFString with 30,000 characters have to be
supported etc. My units generally don't have these limits
(unless explicitly mentioned below). So any number of children in Group
node is supported, SFString may be of any length etc.
VRML authors are limited only by the amount of memory available
on user system, performance of OpenGL implementation etc.

<p>Contents:
<?php echo $toc->html_toc(); ?>

<?php echo $toc->html_section(); ?>

<p><i>All nodes</i> from VRML 2.0 specification are correctly parsed.
The list below lists nodes that are actually handled, i.e. they do
things that they are supposed to do according to "Node reference"
chapter of VRML spec.

<p><i>TODO</i> for all nodes with url fields: for now all URLs
are interpreted as local file names (absolute or relative).
So if a VRML file is available on WWW, you should first download it
(any WWW browser can of couse download it and automatically open view3dscene
for you), remembering to download also any texture/background files
used.

<p>Nodes listed below are fully (except when noted with
<i>TODO</i>) supported :

<ul>
  <li><tt>DirectionalLight</tt>, <tt>PointLight</tt>, <tt>SpotLight</tt>

    <p><i>Note</i>: VRML 2.0 <tt>SpotLight.beamWidth</tt>
    idea cannot be translated to a standard
    OpenGL spotlight, so if you set beamWidth &lt; cutOffAngle then the light
    will not look exactly VRML 2.0-spec compliant.
    Honestly I don't see any sensible way to fix this
    (as long as we talk about real-time rendering using OpenGL).
    And other open-source VRML implementations rendering to OpenGL
    also don't seem to do anything better.

    <p>VRML 2.0 spec requires that at least 8 lights
    are supported. My units can support as many lights as are
    allowed by your OpenGL implementation, which is <i>at least</i> 8.

  <li><p><tt>Background</tt>, <tt>Fog</tt>, <tt>NavigationInfo</tt>,
    <tt>WorldInfo</tt>

    <p><i>Note</i>: <tt>WorldInfo.title</tt>, if set, is displayed by
    view3dscene on window's caption.

  <li><p><tt>Switch</tt>, <tt>Group</tt>, <tt>Transform</tt>

  <li><p><tt>Sphere</tt>, <tt>Box</tt>, <tt>Cone</tt>, <tt>Cylinder</tt>

  <li><p><tt>Shape</tt>, <tt>Appearance</tt>, <tt>Material</tt>

  <li><p><tt>TextureTransform</tt>, <tt>PixelTexture</tt>,
    <tt>ImageTexture</tt>

    <p><i>Note</i>: ImageTexture allows various texture formats,
    including JPEG, PNG, BMP, PPM, RGBE. GIF format is supported
    by running <tt>convert</tt> program from
    <a href="http://www.imagemagick.org/">ImageMagick</a>
    package "under the hood".
    See <?php echo a_href_page('glViewImage', 'glviewimage'); ?>
    documentation for more detailed list.

  <li><p><tt>Inline</tt>, <tt>InlineLoadControl</tt>

  <li><p><tt>LOD</tt>

    <p><i>TODO</i>: But we always render first (best looking) node, ignoring
    distance of node to the viewer.

  <li><p><tt>Anchor</tt>

    <p><i>TODO</i>: But clicking on anchor doesn't do anything for now.
    In other words, for now <tt>Anchor</tt> works just like <tt>Group</tt>.

  <li><p><tt>Text</tt>, <tt>FontStyle</tt>

    <p>Most important properties
    (size, spacing, justify, family, style) are handled fully.

    <p><i>TODO</i>: But some properties are ignored for now:
    <ul>
      <li>FontStyle properties: From section
        <i>6.22.3 Direction and justification</i>
        horizontal, leftToRight, topToBottom fields are ignored
        (and things are always handled like they had default values
        TRUE, TRUE, TRUE). From section <i>6.22.4 Language</i>
        language field is ignored.
      <li><tt>Text</tt>: length, maxEntent are
        ignored (and handled like they had
        default values, which means that the text is not stretched).
    </ul>

  <li><p><tt>Viewpoint</tt>

    <p><i>Note</i>: view3dscene displays also nice menu allowing you to jump
    to any defined viewpoint, displaying viewpoints descriptions.
    Extensive tests of various viewpoint properties, includind fieldOfView,
    are inside <?php
      echo a_href_page('my VRML test suite', 'kambi_vrml_test_suite'); ?>
    in <tt>vrml_2/viewpoint_*.wrl</tt> files.

  <li><p><tt>PointSet</tt>, <tt>IndexedLineSet</tt>, <tt>IndexedFaceSet</tt>,
    <tt>Coordinate</tt>, <tt>Color</tt>,
    <tt>Normal</tt>, <tt>TextureCoordinate</tt>

    <p><i>TODO</i>: only one tiny (practically never used ?) case is not
    implemented: for <tt>IndexedFaceSet</tt>,
    the case when <tt>normalPerVertex</tt> = FALSE and <tt>normal</tt> = NULL
    (i.e. we have specified normals per face)
    is ignored (we just calculate our own normals in this case).

  <li><p><tt>Billboard</tt>, <tt>Collision</tt>

    <p><i>TODO</i>: These two nodes are not really handled:
    they just work like a <tt>Group</tt>. Sometimes that's enough
    for them to look sensible, but it's hardly a real support...

  <li><p><tt>ElevationGrid</tt>

    <p><i>TODO</i>: Fields ignored: color, colorPerVertex
    (always shape material is used or white unlit).
    creaseAngle is not ignored, but is not fully handled:
    we always generate all flat normals (if creaseAngle = 0) or
    all smooth normals (if creaseAngle &lt;&gt; 0).

  <li><p><tt>Extrusion</tt>

    <p>All works (fields: <tt>crossSection, spine, scale, orientation,
    beginCap/EndCap, convex, ccw, solid</tt>, texture coordinates are
    also generated) except<br/>
    <i>TODO</i>: creaseAngle (always flat normals are generated).
</ul>

<p>Prototypes (both external and not) are 100% done and working :)
External prototypes recognize URN of standard VRML 97 nodes, i.e.
<tt>urn:web3d:vrml97:node:Xxx</tt> and standard X3D nodes
(<tt>urn:web3d:x3d:node:Xxx</tt>), see also our extensions URN
on <?php echo a_href_page('Kambi VRML extensions', 'kambi_vrml_extensions'); ?>.

<p><i>TODO</i>: Some general features not implemented yet are listed below.
They all are parsed correctly and consciously (which means that the parser
doesn't simply "omit them to matching parenthesis" or some other dirty
trick like that). But they don't have any effect on the scene. These are:
<ul>
  <li><tt>ROUTE</tt>.
  <li><tt>Script</tt> nodes: no kind of scriping is implemented yet.
  <li>Nodes: sensors, interpolators, geospatial things, NURBS,
    sounds (<tt>AudioClip</tt> and <tt>Sound</tt>),
    <tt>MovieTexture</tt>.
</ul>

<?php echo $toc->html_section(); ?>

<p>Although X3D support is not implemented at all (we even deliberately
don't support X3D files in classic encoding, I want to improve VRML 97
support first), we already have some X3D-specific features implemented.
For now they are available for VRML 97 authors, that is: we support
VRML 97 with some X3D features "backported".
As a reference, I used X3D specification with amendment 1 and revision 1,
that is: the newest X3D specification as of this writing (2007-12).</p>

<p>X3D bits implemented are:</p>

<ul>
  <li><p><tt>Appearance</tt> node has all fields from X3D specification
    allowed (but actually only <tt>shaders</tt> field is handled
    from these X3D-specific Appearance fields).</p></li>

  <li><p>Parts of
    <a href="http://www.web3d.org/x3d/specifications/ISO-IEC-19775-X3DAbstractSpecification_Revision1_to_Part1/Part01/components/shaders.html"><b>programmable shaders component</b></a>
    are implemented: <tt>ComposedShader</tt> and <tt>ShaderPart</tt> nodes
    allow you to write shaders in GLSL language.
    See <?php echo a_href_page_hashlink('VRML extensions about using shaders',
    'kambi_vrml_extensions', 'ext_shaders'); ?> for examples of use.</p></li>
</ul>

<?php echo $toc->html_section(); ?>

<p>I consider VRML 1.0 status as "almost complete".
All nodes and features are handled, with the exception of:

<ul>
  <li>Handling URLs in fields <tt>WWWInline.name</tt> and
    <tt>Texture2.filename</tt>. As for now, only local file names are
    allowed there.
    <!-- Relative paths are resolved with respect to the path of originating
         VRML file. -->

  <li><tt>AsciiText</tt> node's triangles and vertices are not counted
    when writing triangles and vertices counts of the scene.
    <tt>width</tt> field is ignored.

  <li>We can't change current scene clicking on <tt>WWWAnchor</tt>
    nodes (this should be easy to do now, when I implemented
    "Open" menu item).

  <li>Value of <tt>height</tt> / <tt>heightAngle</tt> fields of camera
    nodes are ignored.

    <p><tt>focalDistance</tt> is also ignored, but this
    is allowed by specification. And honestly VRML 1.0 specification
    is so ambiguous about this feature (<i>browser should adjust
    flying speed to reach that point in a reasonable amount of time</i>,
    <i>perhaps the browser can use this as a hint</i>...) that
    I see no reliable way to handle <tt>focalDistance</tt>.

    <p>Fortunately, VRML 2.0 replaced this with <tt>NavigationInfo.speed</tt>
    feature, with clear meaning (basically, it's just a distance per second),
    so please use this instead. (For my engine, you can use
    <tt>NavigationInfo</tt> node even in VRML 1.0 models.)

  <li>I'm always rendering the nearest (first) child of <tt>LOD</tt> node.
    Therefore I'm potentially losing some optimization if the scene
    has reasonably designed <tt>LOD</tt> nodes.

  <li>Partial transparency on textures with full alpha channel (like PNG images).
    For now textures with alpha channel have always simple, all-or-nothing
    transparency. (but partial transparency of Materials is fully supported).

  <li><p>Extensibility features (<tt>isA</tt> and <tt>fields</tt>) are not handled
    fully, although you probably will not notice. For built-in nodes,
    <tt>isA</tt> and <tt>fields</tt> are correctly parsed but ignored.
    For unknown nodes, they are simply omitted up to the matching
    closing parenthesis.

    <p>This means that the only case when you will notice something doesn't
    work is when you use non-standard VRML node but point to a standard
    node with <tt>isA</tt> declaration. Then my engine will ignore
    <tt>isA</tt> declaration, while it should use it to interpret your node
    and (at least partially, when possible) handle it.</p>

    <p>Finishing of handling this VRML 1.0 feature has rather low priority,
    since this mechanism was completely dropped in later VRML versions.
    VRML 2.0 and X3D replaced this by fantastic prototypes mechanism,
    which is basically an extra-powerful and elegant way of doing what
    VRML 1.0 tried to do with <tt>isA</tt> and <tt>fields</tt> feature
    (and VRML prototypes are already handled 100% by our engine).
</ul>

<?php echo $toc->html_section(); ?>

<!-- Besides the collections mentioned below I also tested
on numerous VRML models available on the WWW. -->

<ul>
  <li><p>Tested on <?php
    echo a_href_page('our Kambi VRML test suite', 'kambi_vrml_test_suite'); ?>.

  <li><p>Examples from VRML 2.0 specification and
    <a href="http://accad.osu.edu/~pgerstma/class/vnv/resources/info/AnnotatedVrmlRef/Book.html">
    The Annotated VRML 97 Reference</a> were tested.

  <li><p>Files generated by <a href="http://www.blender3d.org/">Blender</a>
    VRML 2.0 exporter
    are handled. I think that I support fully everything that can be
    generated by this exporter.

  <li><p>From <a href="http://www.itl.nist.gov/div897/ctg/vrml/chaco/chaco.html">
    Chaco's VRML Test Suite</a> passes every file
    (that exists on server &mdash; some are missing, although I fixed missing
    texture for tests) besides <tt>recurse.wrl</tt> &mdash; it's
    an incorrect file, we should produce better error message for it.

  <li><p><a href="http://xsun.sdct.itl.nist.gov/~mkass/vts/html/vrml.html">
    NIST VRML Test Suite</a> results are below.</p>

    <p>Each test was classified as "pass" only if it passed fully.
    Which is a good objective measure,
    but also means that many tests failed
    because unrelated features are not implemented. For example,
    don't be discouraged by many failures in <tt>PROTO</tt> category.
    Prototypes were 100% working in all tests, and I consider their
    implementation as practically finished.
    But many other missing features (sensors, events, interpolators)
    prevented the tests in <tt>PROTO</tt> category from passing completely.</p>

    <table border="1">
      <tr>
        <th>Node Group</th>
        <th>Node</th>
        <th>Test Number</th>
        <th>Result</th>
        <th>Notes</th>
      </tr>
      <tr>
        <td rowspan="59">Appearance</td>
        <td rowspan="12">Appearance</td>
        <td>1</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>4</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>6</td>
        <td class="fail">-</td>
        <td><tt>IndexedFaceSet</tt>, <tt>IndexedLineSet</tt>,
          <tt>PointSet</tt> are OK.
          Handling of <tt>color</tt> for <tt>ElevationGrid</tt>
          not implemented. </td>
      </tr>
      <tr>
        <td>7</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>8</td>
        <td class="fail">-</td>
        <td><tt>IndexedFaceSet</tt> is OK.
          Handling of <tt>color</tt> for <tt>ElevationGrid</tt> not implemented,
          so texture is white instead of blue.</td>
      </tr>
      <tr>
        <td>9</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>10</td>
        <td class="fail">-</td>
        <td><tt>IndexedFaceSet</tt> errorneously modulates texture color
          by specified color. On <tt>ElevationGrid</tt> it "passes", but only
          because we just ignore <tt>color</tt> there, see 6 and 8 cases above.
      </tr>
      <tr>
        <td>11</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>12</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td rowspan="7">FontStyle</td>
        <td>1</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
        <td>Note that the test looks strange because the X axis line
          starts at X = -200. This is an error in the test file.
      </tr>
      <tr>
        <td>4</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>6</td>
        <td class="fail">-</td>
        <td>For horizonal text test passes, but vertical text
          is not implemented yet.
      </tr>
      <tr>
        <td>7</td>
        <td class="fail">-</td>
        <td>Handling Script not implemented yet.
      </tr>
      <tr>
        <td rowspan="34">ImageTexture</td>
        <td>1</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>4</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>6</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>7</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>8</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>9</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>10</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>11</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>12</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>13</td>
        <td class="fail">-</td>
        <td>The texture top is not aligned precisely with text top.
      </tr>
      <tr>
        <td>14</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>15</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>16</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>17</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>18</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>19</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>20</td>
        <td class="fail">-</td>
        <td>Like case 13: The texture top is not aligned precisely with text top.
      </tr>
      <tr>
        <td>21</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>22</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>23</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>24</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>25</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>26</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>27</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>28</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>29</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>30</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>31</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>32</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>33</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>34</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td rowspan="6">Material</td>
        <td>1</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>4</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>6</td>
        <td class="pass">+</td>
      </tr>

      <tr>
        <td colspan="5"><i>...here I skipped some tests, to be checked later...</i></td>
      </tr>

      <tr>
        <td rowspan="41">Geometry</td>
        <td rowspan="14">ElevationGrid</td>
        <td>1</td>
        <td class="pass">+</td>
        <td>Note that by default ElevationGrid is not smoothed (creaseAngle = 0),
          this is following the spec.
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>4</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="fail">-</td>
        <td rowspan="2">color, colorPerVertex on ElevationGrid are ignored for now.
      </tr>
      <tr>
        <td>6</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>7</td>
        <td class="fail">-</td>
        <td rowspan="2">Tested feature (x/zSpacing) works Ok.
          But color, colorPerVertex on ElevationGrid are ignored for now.
      </tr>
      <tr>
        <td>8</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>9</td>
        <td class="pass">+</td>
        <td>The reference image of the test is bad. The result should
          be more obvious (whole rows of quads have the same normal),
          and it is &mdash; with our engine.
      </tr>
      <tr>
        <td>10</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>11</td>
        <td class="pass">+</td>
        <td>Although we use two-sided lighting.
      </tr>
      <tr>
        <td>12</td>
        <td class="fail">-</td>
        <td>Tested feature (generation of smooth normals) works Ok.
          But color, colorPerVertex on ElevationGrid are ignored for now.
      </tr>
      <tr>
        <td>13</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>14</td>
        <td class="pass">+</td>
      </tr>

      <tr>
        <td rowspan="17">Extrusion</td>
        <td>1</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
        <td>Reference images show the incorrect non-uniform scaling
          of the caps. We handle it right.
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>4</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>6</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>7</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>8</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>9</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>10</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>11</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>12</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>13</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>14</td>
        <td class="fail">-</td>
        <td>While generally looks Ok, it seems that our triangulating
          algorithm can't handle this particular shape perfectly.
      </tr>
      <tr>
        <td>15</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>16</td>
        <td class="pass">+</td>
        <td>This links to ElevationGrid creaseAngle test, that passes...
          Has nothing to do with Extrusion actually. (And we do not
          handle creaseAngle on Extrusion yet!)
      </tr>
      <tr>
        <td>17</td>
        <td class="pass">+</td>
      </tr>

      <tr>
        <td rowspan="10">IndexedLineSet</td>
        <td>1</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>4</td>
        <td class="pass">+</td>
        <td rowspan="2">(These tests have nothing to do with IndexedLineSet,
          they are for IndexedFaceSet.)</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>6</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>7</td>
        <td class="pass">+</td>
        <td rowspan="2">(These tests have nothing to do with IndexedLineSet,
          they are for IndexedFaceSet.)</td>
      </tr>
      <tr>
        <td>8</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>9</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>10</td>
        <td class="pass">+</td>
      </tr>

      <tr>
        <td colspan="5"><i>...here I skipped some tests, to be checked later...</i></td>
      </tr>

      <tr>
        <td rowspan="41">Misc</td>
        <td rowspan="18">EXTERNPROTO</td>
        <td>1</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>4</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>6</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>7</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>8</td>
        <td class="fail">-</td>
        <td>Currently, base URL for EXTERNPROTO is from the file where EXTERNPROTO
          is written, not from the file where it's instantiated.
      </tr>
      <tr>
        <td>9</td>
        <td class="fail">-</td>
        <td rowspan="2">
          Scipts are not supported yet. Also, the DEF declaration inside
          a script causes known problem with cycles in VRML graph.</td>
      </tr>
      <tr>
        <td>10</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>11</td>
        <td class="fail">-</td>
        <td rowspan="2">Scipts are not supported yet.</td>
      </tr>
      <tr>
        <td>12</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>13</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>14</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>15</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>16</td>
        <td class="fail">-</td>
        <td rowspan="3">
          Static result is OK, but sensors, interpolators and events do not
          work yet.</td>
      </tr>
      <tr>
        <td>17</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>18</td>
        <td class="fail">-</td>
      </tr>

      <tr>
        <td rowspan="23">PROTO</td>
        <td>1</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>2</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>3</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>4</td>
        <td class="fail">-</td>
        <td>Static renders OK, but sensors, events and routes don't do anything (yet),
          so touch sensor doesn't turn the light on.</td>
      </tr>
      <tr>
        <td>5</td>
        <td class="fail">-</td>
        <td>Like case 4.</td>
      </tr>
      <tr>
        <td>6</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>7A</td>
        <td class="fail">-</td>
        <td>Static renders OK, but sensors, events, routes, interpolators
          don't do anything (yet).</td>
      </tr>
      <tr>
        <td>7B</td>
        <td class="fail">-</td>
        <td>Static renders OK, but sensors, events, routes, interpolators
          don't do anything (yet).</td>
      </tr>
      <tr>
        <td>7C</td>
        <td class="fail">-</td>
        <td>Static renders OK, but Collision and interpolators
          don't do anything (yet).</td>
      </tr>
      <tr>
        <td>7D</td>
        <td class="fail">-</td>
        <td rowspan="3">I didn't even bother testing, like above:
          they use sensors, interpolators and MovieTexture that are not
          implemented yet.
      </tr>
      <tr>
        <td>7E</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>7F</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>7G</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>7H</td>
        <td class="fail">-</td>
        <td><tt>TouchSensor</tt> and <tt>Script</tt> not implemented yet.</td>
      </tr>
      <tr>
        <td>7I</td>
        <td class="invalid">?</td>
        <td>The test file causes recursive inline, so the file cannot be loaded.
          Our engine loads files "eagerly", so it simply hangs.
          It should exit with nice error message, this should be fixed.</td>
      </tr>
      <tr>
        <td>7J</td>
        <td class="fail">-</td>
        <td rowspan="2">Like case 7A.</td>
      </tr>
      <tr>
        <td>7K</td>
        <td class="fail">-</td>
      </tr>

      <tr>
        <td>8</td>
        <td class="fail">-</td>
        <td rowspan="2">Static renders OK, but sensors, events, routes, interpolators
          don't do anything (yet).</td>
      </tr>
      <tr>
        <td>9</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>10</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>11</td>
        <td class="fail">-</td>
        <td rowspan="2">Events etc. do not work yet.</td>
      </tr>
      <tr>
        <td>12</td>
        <td class="fail">-</td>
      </tr>
      <tr>
        <td>13</td>
        <td class="pass">+</td>
      </tr>
      <!--
      <tr>
        <td>14</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>15</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>16</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>17</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>18</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>19</td>
        <td class="pass">+</td>
      </tr>
      <tr>
        <td>20</td>
        <td class="pass">+</td>
      </tr>
      -->

      <tr>
        <td colspan="5"><i>That's enough for now...
          I don't have time to check all the tests.
          If someone wants to do the work and do the remaining
          tests (and document results just like above),
          please contact us by
          <?php echo MAILING_LIST_LINK; ?>.</i>
    </table>

    <p>Cases are marked above as "success" (+) only if they succeed
    completely.
    The table above was modelled after similar page
    <a href="http://www.openvrml.org/doc/conformance.html">
    OpenVRML Conformance Test Results</a>. <!-- See there also
    for some  remarks about invalid tests included in
    NIST test suite. -->

</ul>

<?php
  if (!IS_GEN_LOCAL) {
    php_counter("vrml_implementation_status", TRUE);
  };

  common_footer();
?>
