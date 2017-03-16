<?php
  require_once 'x3d_implementation_common.php';
  require_once 'x3d_extensions_functions.php';
  x3d_status_header('Programmable shaders', 'shaders',
    'This component defines nodes for using high-level shading languages
     available on modern graphic cards.'
  );

  echo castle_thumbs(array(
    array('filename' => 'glsl_teapot_demo.png', 'titlealt' => 'Teapot X3D model rendered with toon shading in GLSL'),
    array('filename' => 'glsl_flutter.png', 'titlealt' => 'GLSL demo &quot;flutter&quot; (from FreeWRL examples)'),
    array('html' =>
      '<div class="thumbs_cell_with_text_or_movie">This movie shows GLSL shaders by our engine.'
      . (!HTML_VALIDATION ?
      '<iframe width="200" height="167" src="https://www.youtube.com/embed/6ecZInTrfak" frameborder="0" allowfullscreen></iframe>'
      : '')
      . '</div>'),
  ));

  $toc = new TableOfContents(
    array(
      new TocItem('Demos', 'demos'),
      new TocItem('Support', 'support'),
      new TocItem('Features and examples', 'examples'),
      new TocItem('Basic example', 'basic', 1),
      new TocItem('Inline shader source code', 'inline', 1),
      new TocItem('Passing values to GLSL shader uniform variables', 'uniforms', 1),
      new TocItem('Passing textures to GLSL shader uniform variables', 'uniforms_tex', 1),
      new TocItem('Passing attributes to GLSL shader', 'attributes', 1),
      new TocItem('Geometry shaders', 'geometry', 1),
      new TocItem('Geometry shaders before GLSL 1.50 not supported', 'geometry_old', 2),
      new TocItem('Macro CASTLE_GEOMETRY_INPUT_SIZE', 'geometry_input_size', 2),
      new TocItem('Inspecting and customizing shaders generated by the engine', 'log_shaders', 1),
      new TocItem('TODOs', 'todos', 1),
    ));
?>

<p>Contents:
<?php echo $toc->html_toc(); ?>

<?php echo $toc->html_section(); ?>

<p>For complete demos of features discussed here,
see the <code>shaders</code> subdirectory inside <?php
echo a_href_page('our VRML/X3D demo models', 'demo_models'); ?>.
You can open them with various <i>Castle Game Engine</i> X3D tools,
in particular with <?php echo a_href_page('view3dscene', 'view3dscene') ?>.

</p>

<?php echo $toc->html_section(); ?>

<p><?php echo x3d_node_link('ComposedShader'); ?> and
<?php echo x3d_node_link('ShaderPart'); ?> nodes
allow you to write shaders in the <i>OpenGL shading language (GLSL)</i>.
These are standard X3D nodes to replace the default browser rendering with shaders.
To learn GLSL, see:

<ul>
  <li><a href="https://www.opengl.org/sdk/docs/man4/">The GLSL function reference</a>.
    Be careful: the reference linked here describes both GLSL and OpenGL API.
    You can ignore the functions named <code>glXxx</code>,
    they are part of the OpenGL API,
    and they not useful to a shader author.
  <li><a href="https://en.wikipedia.org/wiki/OpenGL_Shading_Language">GLSL description at Wikipedia</a> and
    <a href="https://www.khronos.org/opengl/wiki/OpenGL_Shading_Language">GLSL description at Khronos wiki</a>.
</ul>

<p>If you're interested in shaders, we strongly encourage you to
try also <?php echo a_href_page('our compositing shaders extensions for X3D, with <code>Effect</code> and related nodes',
'compositing_shaders') ?>. They allow to write shader code that can cooperate
with standard browser rendering, and define rendering effects that can be
easily reused and combined.

<?php echo $toc->html_section(); ?>

<?php echo $toc->html_section(); ?>

<p>Examples below are in the classic (VRML) encoding.
To use shaders add inside the <code>Appearance</code> node code like</p>

<?php echo vrmlx3d_highlight(
'shaders ComposedShader {
  language "GLSL"
  parts [
    ShaderPart { type "VERTEX"   url "my_shader.vs" }
    ShaderPart { type "FRAGMENT" url "my_shader.fs" }
  ]
}'); ?>

<p>The simplest <i>vertex shader code</i> to place inside <code>my_shader.vs</code> file
would be:

<?php echo glsl_highlight(
'void main(void)
{
  gl_Position = ftransform();
}'); ?>

<p>The simplest <i>fragment shader code</i> to place inside <code>my_shader.fs</code> file
would be:

<?php echo glsl_highlight(
'void main(void)
{
  gl_FragColor = vec4(1.0, 0.0, 0.0, 1.0); /* red */
}'); ?>

<?php echo $toc->html_section(); ?>

<p>You can directly write the shader source code inside an URL field
(instead of putting it in an external file).
The best way to do this, following the standards, is to use
the <a href="https://en.wikipedia.org/wiki/Data_URI_scheme">data URI</a>.
In the simplest case, just start the URL with "<code>data:text/plain,</code>"
and then write your shader code.</p>

<p>A simplest example:</p>

<?php echo vrmlx3d_highlight(
'shaders ComposedShader {
  language "GLSL"
  parts [
    ShaderPart { type "VERTEX"
      url "data:text/plain,
      void main(void)
      {
        gl_Position = ftransform();
      }" }

    ShaderPart { type "FRAGMENT"
      url "data:text/plain,
      void main(void)
      {
        gl_FragColor = vec4(1.0, 0.0, 0.0, 1.0); /* red */
      }" }
  ]
}'); ?>

<p><a href="http://svn.code.sf.net/p/castle-engine/code/trunk/demo_models/shaders/shaders_inlined.x3dv">Another example: shaders_inlined.x3dv</a>.</p>

<p>In case of the X3D XML encoding, you can also place
shader source code inside the CDATA.</p>

<p>As an extension (but compatible at least with
<a href="http://instant-reality.com/">InstantPlayer</a>)
we also recognize URL as containing direct shader source if it
has any newlines and doesn't start with any URL protocol.
But it's better to use "<code>data:text/plain,</code>" mentioned above.

<?php echo $toc->html_section(); ?>

<p>You can set uniform variables for your shaders from VRML/X3D,
just add lines like</p>

<?php echo vrmlx3d_highlight(
'inputOutput SFVec3f UniformVariableName 1 0 0'); ?>

<p>to your ComposedShader node. These uniforms may also be modified by
events (when they are <code>inputOutput</code> or <code>inputOnly</code>),
for example here's a simple way to pass the current time (in seconds)
to your shader:

<?php echo vrmlx3d_highlight(
'# somewhere within Appearance:
shaders DEF MyShader ComposedShader {
  language "GLSL"
  parts [
    ShaderPart { type "VERTEX" url "my_shader.vs" }
    ShaderPart { type "FRAGMENT" url "my_shader.fs" }
  ]
  inputOnly SFTime time
}

# somewhere within grouping node (e.g. at the top-level of VRML/X3D file) add:
DEF MyProximitySensor ProximitySensor { size 10000000 10000000 10000000 }
DEF MyTimer TimeSensor { loop TRUE }
ROUTE MyProximitySensor.enterTime TO MyTimer.startTime
ROUTE MyTimer.elapsedTime TO MyShader.time'); ?>

<p>The <code>ProximitySensor</code> node above is useful to make
time start ticking from zero when you open VRML/X3D, which makes the float
values in <code>MyTimer.elapsedTime</code> increase from zero.
Which is usually useful, and avoids having precision problems
with huge values of <code>MyTimer.time</code>. See <?php echo a_href_page('notes
about VRML / X3D time origin', 'x3d_time_origin_considered_uncomfortable'); ?>
 for more details.

<p>Most field types may be passed to appropriate GLSL uniform
values. You can even set GLSL vectors and matrices.
You can use VRML/X3D multiple-value fields to set
GLSL array types.
We support all mappings between VRML/X3D and GLSL types
for uniform values (that are mentioned in X3D spec).
<i>TODO: except the (rather obscure) <code>SFImage</code> and <code>MFImage</code>
types, that cannot be mapped to GLSL now.</i>
</p>

<?php echo $toc->html_section(); ?>

<p>You can also specify texture node (as <code>SFNode</code> field, or an array
of textures in <code>MFNode</code> field) as a uniform field value.
Engine will load and bind the texture and pass to GLSL uniform variable
bound texture unit. This means that you can pass in a natural way
texture node to a GLSL <code>sampler2D</code>, <code>sampler3D</code>,
<code>samplerCube</code>, <code>sampler2DShadow</code> and such.</p>

<?php echo vrmlx3d_highlight(
'shaders ComposedShader {
  language "GLSL"
  parts [
    ShaderPart { type "VERTEX"
      url "data:text/plain,
      void main(void)
      {
        gl_Position = ftransform();
      }" }

    ShaderPart { type "FRAGMENT" url
    "data:text/plain,

     uniform sampler2D texture_one;
     uniform sampler2D texture_two;

     void main()
     {
       gl_FragColor = gl_Color *
         max(
           texture2D(texture_one, gl_TexCoord[0].st),
           texture2D(texture_two, gl_TexCoord[1].st));
     }
    " }
  ]
  initializeOnly SFNode texture_one ImageTexture { url "one.png" }
  initializeOnly SFNode texture_two ImageTexture { url "two.png" }
}'); ?>

<p>A full working version of this example can be found
in <?php echo a_href_page('our VRML/X3D demo models', 'demo_models'); ?>
 (look for file <code>shaders/simple_multitex_shaders.x3dv</code>),
<a href="http://svn.code.sf.net/p/castle-engine/code/trunk/demo_models/shaders/simple_multitex_shaders.x3dv">or see it here</a>.
</p>

<p>When using GLSL shaders in X3D you should pass all
needed textures to them this way. Normal <code>appearance.texture</code>
is ignored when using shaders. However, in our engine,
we have a special case to allow you to specify textures also
in traditional <code>appearance.texture</code> field: namely,
when <code>ComposedShader</code> doesn't contain any texture nodes,
we will still bind <code>appearance.texture</code>. This e.g. allows
you to omit declaring texture nodes in <code>ComposedShader</code>
field if you only have one texture, it also allows renderer to
reuse OpenGL shader objects more (as you will be able to DEF/USE
in X3D <code>ComposedShader</code> nodes even when they use different
textures). But this feature should
not be used or depended upon in the long run.</p>

<p>Note that for now you have to pass textures in VRML/X3D fields
(<code>initializeOnly</code> or <code>inputOutput</code>).
TODO: Using <code>inputOnly</code> event to pass texture node to GLSL shader
temporarily does not work, just use <code>initializeOnly</code> or <code>inputOutput</code> instead.</p>

<?php echo $toc->html_section(); ?>

<p>You can also pass per-vertex attributes to your shader.
You can pass floats, vectors and matrices.
The way do use this of course follows X3D specification,
see <?php echo x3d_node_link('FloatVertexAttribute'); ?>,
<?php echo x3d_node_link('Matrix3VertexAttribute'); ?>,
<?php echo x3d_node_link('Matrix4VertexAttribute'); ?> nodes.
You can place them in the <code>attrib</code> field of most geometry nodes
(like <code>IndexedFaceSet</code>).</p>

<p><a href="http://svn.code.sf.net/p/castle-engine/code/trunk/demo_models/shaders/attributes.x3dv">Example attributes.x3dv</a>,
showing how to pass elevation grid heights by the shader attributes.</p>

<?php echo $toc->html_section(); ?>

<?php
  echo castle_thumbs(array(
    array('filename' => 'geometry_shader_fun_smoothing.png', 'titlealt' => 'Geometry shaders fun smoothing demo'),
  ));
?>

<p>We support <i>geometry shaders</i>
(in addition to standard <i>vertex</i> and <i>fragment shaders</i>).
To use them, simply set <code>ShaderPart.type</code> to <code>"GEOMETRY"</code>,
and put code of your geometry shader inside <code>ShaderPart.url</code>.</p>

<p><b>What is a geometry shader?</b>
A geometry shader is executed once for each primitive, like once for each triangle.
Geometry shader works <i>between</i> the vertex shader and fragment shader
&mdash; it knows all the outputs from the vertex shader,
and is responsible for passing them to the rasterizer.
<!--
and it generates inputs to the fragment shader.
(More precisely, geometry shader generates inputs for the rasterizer,
and the interpolated values will be given to the fragment shader.)
-->
Geometry shader uses the information about given primitive: vertex positions
from vertex shader, usually in eye or object space,
and all vertex attributes.
A single geometry shader may generate any number of primitives
(separated by the <code>EndPrimitive</code> call), so you can easily "explode"
a simple input primitive into a number of others.
You can also delete some original primitives based on some criteria.
The type of the primitive may be changed by the geometry shader
&mdash; for example, triangles may be converted to points or the other way around.</p>

<p>Examples of geometry shaders with <code>ComposedShader</code>:</p>

<ul>
  <li><a href="http://svn.code.sf.net/p/castle-engine/code/trunk/demo_models/shaders/geometry_shader.x3dv">Download
    a basic example X3D file with geometry shaders</a></li>
  <li><a href="http://svn.code.sf.net/p/castle-engine/code/trunk/demo_models/shaders/geometry_shader_fun_smoothing.x3dv">Another
    example of geometry shaders: geometry_shader_fun_smoothing</a>.</li>
</ul>

<p>We have also a more flexible approach to geometry shaders
as part of our <?php echo a_href_page('compositing shaders', 'compositing_shaders'); ?>
 extensions. The most important advantage is that you can implement
only the geometry shader, and use the default vertex and fragment shader code
(that will do the boring stuff like texturing, lighting etc.).
Inside the geometry shader you have functions <code>geometryVertexXxx</code>
to pass-through or blend input vertexes in any way you like.
Everything is described in detail in our
<?php echo a_href_page('compositing shaders documentation', 'compositing_shaders'); ?>,
 in particular see the <a href="https://castle-engine.sourceforge.io/compositing_shaders_doc/html/chapter.geometry_shaders.html">the chapter "Extensions for geometry shaders"</a>.</p>

<p>Examples of geometry shaders with <code>Effect</code>:</p>

<ul>
  <li><a href="http://svn.code.sf.net/p/castle-engine/code/trunk/demo_models/compositing_shaders/geometry_shader_simple.x3dv">geometry_shader_simple</a></li>
  <li><a href="http://svn.code.sf.net/p/castle-engine/code/trunk/demo_models/compositing_shaders/geometry_shader_effects.x3dv">geometry_shader_effects</a></li>
  <li><a href="http://svn.code.sf.net/p/castle-engine/code/trunk/demo_models/compositing_shaders/geometry_shader_optional.x3dv">geometry_shader_optional</a></li>
</ul>

<?php echo $toc->html_section(); ?>

<p>Our implementation of geometry shaders is directed only at geometry
shaders as available in the OpenGL core 3.2 and later (GLSL version is 1.50 or later).</p>

<!-- http://www.lighthouse3d.com/tutorials/glsl-core-tutorial/geometry-shader/ -->

<p>Earlier OpenGL and GLSL versions had geometry shaders
only available through extensions:
<a href="http://www.opengl.org/registry/specs/ARB/geometry_shader4.txt">ARB_geometry_shader4</a>
or <a href="http://www.opengl.org/registry/specs/EXT/geometry_shader4.txt">EXT_geometry_shader4</a>.
They had the same purpose, but the syntax and calls were different and incompatible.
For example, vertex positions were in <code>gl_PositionIn</code> instead of <code>gl_in</code>.

<p>The most important incompatibility was that the <i>input
and output primitive types</i>, and the <i>maximum number of vertices
generated</i>, were specified outside of the shader source code.
To handle this, an X3D browser would have to do special OpenGL calls
(<code>glProgramParameteriARB/EXT</code>),
and these additional parameters must be placed inside the special
fields of the <code>ComposedShader</code>.
<a href="http://doc.instantreality.org/documentation/nodetype/ComposedShader/">InstantReality
ComposedShader</a> adds additional fields <code>geometryInputType</code>,
<code>geometryOutputType</code>, <code>geometryVerticesOut</code> specifically
for this purpose
(see also the bottom of <a href="http://doc.instantreality.org/tutorial/shader-programs/">InstantReality
shaders overview</a>).</p>

<!--
http://www.opengl.org/wiki/Geometry_Shader
http://www.opengl.org/wiki/Tutorial4:_Using_Indices_and_Geometry_Shaders_%28C_/SDL%29
 -->

<p>See <a href="https://en.wikipedia.org/w/index.php?title=OpenGL_Shading_Language&direction=prev&oldid=738650243#A_sample_trivial_GLSL_geometry_shader">simple example on Wikipedia of GLSL geometry shader differences before and after GLSL 1.50</a>.</p>

<p>We have decided to <b>not implement the old style geometry shaders</b>.
The implementation would complicate the code
(need to handle new fields of the <code>ComposedShader</code> node),
and have little benefit (usable only for old OpenGL versions;
to make geometry shaders work with both old and new OpenGL versions,
authors would have to provide two separate versions of their geometry shaders).</p>

<p>So we just require new geometry shaders to conform to GLSL &gt;= 1.50
syntax.
On older GPUs, you will not be able to use geometry shaders at all.</p>

<?php echo $toc->html_section(); ?>

<p>Unfortunately, ATI graphic cards have problems with geometry shader inputs.
When the input array may be indexed by a variable (not a constant),
it has to be declared with an explicit size.
Otherwise you get shader compilation errors
<i>'[' : array must be redeclared with a size before being indexed with a variable</i>.
The input size actually depends
on the input primitive, so in general you have to write:</p>

<?php echo glsl_highlight(
'in float my_variable[gl_in.length()];'); ?>

<p>Unfortunately, the above syntax does not work on NVidia,
that does not know that <code>gl_in.length()</code> is constant.
On the other hand, NVidia doesn't require input array declaration.
So you have to write:</p>

<?php echo glsl_highlight(
'in float my_variable[];'); ?>

<p>That's very cool, right? We know how to do it on ATI, but it doesn't
work on NVidia. We know how to do it on NVidia, but it doesn't work on ATI.
Welcome to the world of modern computer graphics :)</p>

<p>To enable you to write simple and robust geometry shaders,
our engine allows you to use a macro <code>CASTLE_GEOMETRY_INPUT_SIZE</code>
that expands to appropriate text (or nothing) for current GPU.
So you can just write:</p>

<?php echo glsl_highlight(
'in float my_variable[CASTLE_GEOMETRY_INPUT_SIZE];'); ?>

<?php echo $toc->html_section(); ?>

<p>The engine can generate GLSL shaders
(as X3D <code>ComposedShader</code> node) to render your shapes.
This happens automatically under the hood in many situations (for example
when your shapes require bump mapping or shadow maps, or when you
compile for OpenGLES &gt;= 2). You can run
<?php echo a_href_page('view3dscene', 'view3dscene'); ?>
 with a command-line option <code>--debug-log-shaders</code>
and force rendering using shaders (for example by menu option
<i>View -&gt; Shaders -&gt; Enable For Everything</i>)
to view the generated GLSL code for your shaders in the console.
You can even copy them, as a <code>ComposedShader</code> node,
and adjust, if you like.

<?php echo $toc->html_section(); ?>

<p>TODO: <code>activate</code> event doesn't work to relink the GLSL
program now. (<code>isSelected</code> and <code>isValid</code> work perfectly for any
X3DShaderNode.)

<?php
  x3d_status_footer();
?>
