<div class="messages"></div>
<form action="">
    <?php if(count($countries) > 0): ?>
        <div class="form-group">
            <label for="countries"><?= __('Country\'s'); ?></label>
            <select id="countries" name="countries" class="form-control">
                <option value="">-------</option>
                <?php foreach ($countries as $country): ?>
                    <option data-country="<?= $country->country_code ?>" value="<?= $country->id ?>"><?= $country->country ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="zipCode"><?= __('Zip Code'); ?></label>
            <input type="text" name="zipCode" id="zipCode" class="form-control">
        </div>

        <button type="button" id="searchCountry" class="form-control"><?= __('Search') ?></button>
        <input type="hidden" id="countryListNonce" value="<?php echo wp_create_nonce('countryListNonce'); ?>"/>
    <?php else: ?>
        <i>No countries available...</i>
    <?php endif; ?>
</form>