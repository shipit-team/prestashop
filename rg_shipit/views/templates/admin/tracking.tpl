<div class="card" >
{if $form_type == 1}
  <div class="card-header">
    <img style="float: left;" src="{$logoPath}" id="module_logo"><h4 style="margin: 2px 0px 0px 7px;" class="card-title">ENVÍO SHIPIT</h4>
  </div>
  <div class="alert alert-warning" role="alert">
    La información de este pedido fue eliminada o no existe.
  </div>
{/if}
{if $form_type == 2}
  <div class="card-header">
    <img style="float: left;" src="{$logoPath}" id="module_logo"><h4 style="margin: 2px 0px 0px 7px;" class="card-title">ENVÍO SHIPIT</h4>
  </div>
  <div class="card-body">
  <form id="module_form" method="post" enctype="multipart/form-data" novalidate>
    <div class="form-group">
      <label for="label_courier">Courier</label>
      <input type="hidden" name="submitGenerateShipit" value="1" />
      <input type="text" class="form-control" id="courier" name="courier" value="{$courierClientName}" disabled>
    </div>
    <button type="submit" value="1"	id="module_form_submit_btn" name="submitGenerateShipit" class="btn btn-primary">Generar Envío</button>
  </form>
  </div>
{/if}
{if $form_type == 3}
  <div class="card-header">
    <img style="float: left;" src="{$logoPath}" id="module_logo"><h4 style="margin: 2px 0px 0px 7px;" class="card-title">ENVÍO SHIPIT</h4>
  </div>
  <div class="card-body">
  <div class="form-group">
      <label for="label_id">SHIPIT ID</label>
      <input type="text" class="form-control"  value="{$shipment.shipit_id}">
  </div>
    <div class="form-group">
      <label for="label_status">Estado Shipit</label>
      <input type="text" class="form-control"  value="{$shipment.status}">
  </div>
  <div class="form-group">
      <label for="label_tracking">Empaque</label>
      <input type="text" class="form-control"  value="{$shipment.packing}">
  </div>
  <div class="form-group">
      <label for="label_tracking">Número de Rastreo</label>
      <input type="text" class="form-control"  value="{$shipment.tracking}">
  </div>
  <div class="form-group">
      <label for="label_courier">Courier</label>
      <input type="text" class="form-control"  value="{$shipment.courier}">
  </div>
  <div class="form-group">
      <label for="label_courier">Última actualización envío</label>
      <input type="text" class="form-control"  value="{$shipment.date_upd}">
  </div>
  </div>
{/if}
</div>
