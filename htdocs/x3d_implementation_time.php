<?php
  require_once 'x3d_implementation_common.php';
  x3d_status_header('Time', 'time',
    'This component describes the time-dependent nodes, that react
     directly to the time changes. The <tt>TimeSensor</tt> node
     defined here simply propagates the time information to other nodes
     (and, as such, is the basis of almost all VRML/X3D animations).'

     /*
     Other time-dependent nodes include
     the <tt>MovieTexture</tt>
     (see ' . hack_a_href_page('Texturing component', 'x3d_implementation_texturing') . ')
     and <tt>AudioClip</tt> (see ' .
     hack_a_href_page('Sound component', 'x3d_implementation_sound') . '.'
     */
  );
?>

<ul>
  <li><p><?php echo x3d_node_link('TimeSensor'); ?>:</p>

    <p>Works completely, following X3D 3.2 spec. Except:

    <p><i>TODO:</i> We do not gracefully react to enabled := FALSE
    on active node (see X3D TimeSensor spec
    "If a set_enabled FALSE event is received while the TimeSensor node is running, the sensor performs the following actions:...").

    <p>In fact, the whole handling of <tt>enabled = FALSE</tt>
    is shaky. Some output events will not be generated when
    not enabled, but it's not a fully spec-compliant implementation.</p>
</ul>

<p><i>Note:</i> "Time origin" in our engine follows VRML/X3D standard
(time = 0 means "January 1, 1970"), but it can be changed
by <?php echo a_href_page_hashlink(
'our extension <tt>KambiNavigationInfo.timeOriginAtLoad</tt>',
'x3d_extensions',
'section_ext_time_origin_at_load'); ?>.</p>

<?php
  x3d_status_footer();
?>