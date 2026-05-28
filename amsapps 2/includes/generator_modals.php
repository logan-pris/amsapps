<div class="modal fade" id="newContractorModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Contractor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" id="nc_name">
          </div>
          <div class="col-md-6">
            <label class="form-label">Department</label>
            <input type="text" class="form-control" id="nc_department">
          </div>
          <div class="col-md-6">
            <label class="form-label">Role</label>
            <input type="text" class="form-control" id="nc_role">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="tele" class="form-control" id="nc_phone">
          </div>
          <div class="col-md-12">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" id="nc_email">
          </div>
          <div class="col-md-6">
            <label class="form-label">Job Rate</label>
            <input type="number" class="form-control" id="nc_jobrate">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="saveNewContractor()">Save Contractor</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="newJobModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Job</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Job Number</label>
            <input type="text" class="form-control" id="nj_job_number">
          </div>
          <div class="col-md-8">
            <label class="form-label">Job Name</label>
            <input type="text" class="form-control" id="nj_job_name">
          </div>
          <div class="col-md-12">
            <label class="form-label">Job Location</label>
            <input type="text" class="form-control" id="nj_job_location">
          </div>
          <div class="col-md-6">
            <label class="form-label">Load In Date</label>
            <input type="date" class="form-control" id="nj_load_in">
          </div>
          <div class="col-md-6">
            <label class="form-label">Load Out Date</label>
            <input type="date" class="date form-control" id="nj_load_out">
          </div>
          <div class="col-md-6">
            <label class="form-label">PM Name</label>
            <input type="text" class="form-control" id="nj_pm_name">
          </div>
          <div class="col-md-6">
            <label class="form-label">PM Contact</label>
            <input type="text" class="form-control" id="nj_pm_contact">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="saveNewJob()">Save Job</button>
      </div>
    </div>
  </div>
</div>