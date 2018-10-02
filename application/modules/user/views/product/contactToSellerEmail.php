<div class="modal-header">
  <h4 class="modal-title text-center">Contact To Seller Form</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <form class="block" action="<?php echo base_url('user/product/submitContactToSellerEmail/'.$am_id);?>" method="post">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="row">
      <div class="col-md-12 multi-horizontal" data-for="name">
        <div class="form-group">
          <label class="form-control-label ">Name</label>
              <input class="form-control input" name="contact_us[name]" data-form-field="Name" placeholder="Your Name" value="<?php echo $user['name'];?>" id="name-form4-4v" type="text" <?php if($user['name'] == ''){?>required <?php }?>>
          </div>
        </div>
        <div class="col-md-12" data-for="email">
          <div class="form-group">
            <label class="form-control-label ">Email</label>
              <input class="form-control input" name="contact_us[email]" data-form-field="Email" placeholder="Email" required="" id="email-form4-4v" type="email" value="<?php echo $user['email'];?>" <?php if($user['email'] == ''){?>required<?php }?>>
          </div>
        </div>

        <div class="col-md-12 multi-horizontal" data-for="phone">
          <div class="form-group">
            <label class="form-control-label ">Phone No</label>
              <input class="form-control input" name="contact_us[mobile]" data-form-field="Phone" placeholder="Phone" required="" id="phone-form4-4v" type="number" value="<?php echo $user['mobile'];?>" <?php if($user['mobile'] == ''){?>required<?php }?>>
          </div>
        </div>
      
        <div class="col-md-12" data-for="message">
          <div class="form-group">
            <label class="form-control-label ">Message</label>
              <textarea class="form-control input" name="contact_us[desccription]" rows="3" required="" data-form-field="Message" placeholder="Message" style="resize:none" id="message-form4-4v"></textarea>
          </div>
        </div>
        <div class="input-group-btn col-md-12">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
  </form>
</div>
