<?php if(request('customfields_type') == 'leads'): ?>
<tr class="customfields-table-settings hidden toggle-table-settings-row  toggle-table-settings-row-<?php echo e($field->customfields_id); ?>"
    id="customfields_settings_row_<?php echo e($field->customfields_id); ?>">
    <td class="b-0">
        <div class="row">
            <!--dropdown options-->
            <?php echo $__env->make('pages.settings.sections.customfields.settings.dropdown', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <!--use in standard (app) form-->
            <div class="col-4">
                <input type="checkbox" id="customfields_standard_form_status[<?php echo e($field->customfields_id); ?>]"
                    name="customfields_standard_form_status[<?php echo e($field->customfields_id); ?>]"
                    class="filled-in chk-col-pink" <?php echo e(runtimePrechecked($field->customfields_standard_form_status)); ?>>
                <label
                    for="customfields_standard_form_status[<?php echo e($field->customfields_id); ?>]"><?php echo app('translator')->get('lang.use_in_standard_form'); ?></label>
            </div>
            <!--show on lead card-->
            <div class="col-4">
                <input type="checkbox" id="customfields_show_lead_summary[<?php echo e($field->customfields_id); ?>]"
                    name="customfields_show_lead_summary[<?php echo e($field->customfields_id); ?>]"
                    class="filled-in chk-col-light-blue"
                    <?php echo e(runtimePrechecked($field->customfields_show_lead_summary)); ?>>
                <label
                    for="customfields_show_lead_summary[<?php echo e($field->customfields_id); ?>]"><?php echo app('translator')->get('lang.show_lead'); ?></label>
            </div>
            <!--show in filter [FUTURE]-->
            <div class="col-4 hidden">
                <input type="checkbox" id="customfields_show_filter_panel[<?php echo e($field->customfields_id); ?>]"
                    name="customfields_show_filter_panel[<?php echo e($field->customfields_id); ?>]"
                    class="filled-in chk-col-light-blue"
                    <?php echo e(runtimePrechecked($field->customfields_show_filter_panel)); ?>>
                <label
                    for="customfields_show_filter_panel[<?php echo e($field->customfields_id); ?>]"><?php echo app('translator')->get('lang.show_in_filter_panel'); ?></label>
            </div>

            <!--field status-->
            <div class="col-4">
                <div class="switch" id="customfields_settings_status_<?php echo e($field->customfields_id); ?>">
                    <?php echo app('translator')->get('lang.status'); ?> 
                    <label>
                        <input <?php echo e(runtimePrechecked($field->customfields_status ?? '')); ?> type="checkbox" name="customfields_status[<?php echo e($field->customfields_id); ?>]">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>

            <!--delete-field-->
            <div class="col-4">
                <button type="button" class="btn btn-danger btn-sm btn-sm confirm-action-danger"
                    data-confirm-title="<?php echo app('translator')->get('lang.delete_item'); ?>" data-confirm-text="<?php echo app('translator')->get('lang.are_you_sure'); ?>"
                    data-ajax-type="DELETE" data-url="<?php echo e(url('/settings/customfields/'.$field->customfields_id)); ?>">
                    <?php echo app('translator')->get('lang.delete_item'); ?>
                </button>
            </div>
    </td>
    <td class="b-0">
    </td>
</tr>
<?php endif; ?><?php /**PATH /var/www/html/pms.skunktest.work/application/resources/views/pages/settings/sections/customfields/settings/leads.blade.php ENDPATH**/ ?>