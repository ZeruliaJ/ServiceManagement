$(document).ready(function () {

    $(document).on('change', 'select[name="business_zone_id"]', function () {
        var selectedZone = $(this).val();
        var $region = $('select[name="business_region_id"]');
        $region.val(null).trigger('change');
        $region.empty();
        $region.attr('data-field1-id', selectedZone);

        var $district = $('select[name="business_district_id"]');
        $district.val(null).trigger('change');
        $district.empty();

        var $town = $('select[name="business_town_id"]');
        $town.val(null).trigger('change');
        $town.empty();

        destroySelect2($region, window.Lang.select_region);
        destroySelect2($district, window.Lang.select_district);
        destroySelect2($town, window.Lang.select_town);
    });

    $(document).on('change', 'select[name="business_region_id"]', function () {
        var selectedRegion = $(this).val();

        var $district = $('select[name="business_district_id"]');
        $district.val(null).trigger('change');
        $district.empty();

        var $town = $('select[name="business_town_id"]');
        $town.val(null).trigger('change');
        $town.empty();

        $district.attr('data-field1-id', selectedRegion);

        destroySelect2($district, window.Lang.select_district);
        destroySelect2($town, window.Lang.select_town);
    });

    $(document).on('change', 'select[name="business_district_id"]', function () {
        var selectedDistrict = $(this).val();

        var $town = $('select[name="business_town_id"]');
        $town.val(null).trigger('change');
        $town.empty();
        $town.attr('data-field1-id', selectedDistrict);

        destroySelect2($town, window.Lang.select_town);
    });

    $(document).on('change', 'select[name="residential_zone_id"]', function () {
        var selectedZone = $(this).val();
        var $region = $('select[name="residential_region_id"]');
        $region.val(null).trigger('change');
        $region.empty();
        $region.attr('data-field1-id', selectedZone);

        var $district = $('select[name="residential_district_id"]');
        $district.val(null).trigger('change');
        $district.empty();

        var $town = $('select[name="residential_town_id"]');
        $town.val(null).trigger('change');
        $town.empty();

        destroySelect2($region, window.Lang.select_region);
        destroySelect2($district, window.Lang.select_district);
        destroySelect2($town, window.Lang.select_town);
    });

    $(document).on('change', 'select[name="residential_region_id"]', function () {
        var selectedRegion = $(this).val();

        var $district = $('select[name="residential_district_id"]');
        $district.val(null).trigger('change');
        $district.empty();

        var $town = $('select[name="residential_town_id"]');
        $town.val(null).trigger('change');
        $town.empty();

        $district.attr('data-field1-id', selectedRegion);
        destroySelect2($district, window.Lang.select_district);
        destroySelect2($town, window.Lang.select_town);
    });

    $(document).on('change', 'select[name="residential_district_id"]', function () {
        var selectedDistrict = $(this).val();
        var $town = $('select[name="residential_town_id"]');
        $town.val(null).trigger('change');
        $town.empty();
        $town.attr('data-field1-id', selectedDistrict);
        destroySelect2($town, window.Lang.select_town);
    });

    $(document).on('change', '.nature-business-checkbox', function () {
        let isOtherChecked = $('.nature-business-checkbox[value="Other"]').is(':checked');

        if (isOtherChecked)
        {
            $('#nature-other-wrapper').slideDown();
        }
        else
        {
            $('#nature-other-wrapper').slideUp();
            $('#nature_of_business_other').val('');
        }
    });

    $(document).on('change','select[name="is_pep"]', function () {
        let isPep = $(this).val();
        let pepFields = [
            'input[name="emergency_fullname"]',
            'input[name="emergency_contact_number"]',
            'input[name="emergency_residence"]',
            'textarea[name="emergency_pep_details"]'
        ];

        if (isPep === 'yes')
        {
            $('.section-pep-details').show('slow');
            // Add required attribute and asterisk
            pepFields.forEach(function(selector) {
                let field = $(selector);
                field.attr('required', true);
                let label = field.closest('.col-lg-4, .col-lg-12').find('label').first();
                if (label.length && !label.find('.text-danger').length) {
                    label.append(' <span class="text-danger pep-asterisk">*</span>');
                }
            });
        }
        else
        {
            $('.section-pep-details').hide('slow');
            // Clear values and remove required
            pepFields.forEach(function(selector) {
                let field = $(selector);
                field.val('').removeAttr('required');
                let label = field.closest('.col-lg-4, .col-lg-12').find('label').first();
                label.find('.pep-asterisk').remove();
            });
        }
    });

    $(document).on('change', '.kyc-source-select', function () {
        let selectedSource = $(this).val();
        if (selectedSource == 'Local Supplier')
        {
            $('#country_origin_wrapper').hide('slow');
            $('#country_of_origin').val(null).trigger('change');
        }
        else if(selectedSource == 'Import')
        {
            $('#country_origin_wrapper').show('slow');
        }
    });
});

