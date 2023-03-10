<!-- All Modals -->

<!-- About -->
<div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="aboutModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-uppercase" id="exampleModalLongTitle"><?php echo $lang['about']; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="content-text-modal">
          <p><?php echo  $lang['aboutUsTxt']; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- // about -->

<!-- Contact -->
<div class="modal fade" id="ContactModal" tabindex="-1" role="dialog" aria-labelledby="ContactModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo $lang['modal_contact_tt']; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="content-text-modal">
          <p><?php echo $lang['modal_contact_txt']; ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- // Contact -->

<!-- Comment nous écouter / How to listen -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="modal_format_tt" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bolder text-uppercase" id="exampleModalLongTitle">
          <?php echo $lang['modal_format_tt']; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="content-text-modal">
        <?php echo $lang['modal_format_txt']; ?>
        <ul>
          <li><span class="highlighted"><a href="<?php echo $lang['modal_m3U_link']; ?>"><?php echo $lang['modal_m3U_txt']; ?></a></span></li>
          <li><span class="highlighted"><a href="<?php echo $lang['modal_pls_link']; ?>"><?php echo $lang['modal_pls_txt']; ?></a></span></li>
          <li><span class="highlighted"><a href="<?php echo $lang['modal_xspf_link']; ?>"><?php echo $lang['modal_xspf_txt']; ?></a></span></li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- // Comment nous écouter / How to listen -->

<!-- credits -->
<div class="modal fade bd-credit" id="creditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bolder text-uppercase" id="exampleModalLongTitle">Crédits</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="content-text-modal">
        <ul>
          <li>Coded with<a href='https://plyr.io' target='_blank'>plyr.io</a></li>
          <li>Player : <a href='https://plyr.io' target='_blank'>plyr.io</a></li>
          <li>Requests section inspired by <a href='https://stewartswebworks.com' target='_blank'>StewartsWebWorks.com</a></li>
          <li>Icons by <a href='https://www.fontawesome.com' target='_blank'>FontAwesome</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- // credits -->