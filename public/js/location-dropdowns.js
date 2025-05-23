/**
 * Script untuk mengelola cascading dropdowns negara, provinsi, dan kota
 */
document.addEventListener('DOMContentLoaded', function() {
    const countryDropdown = document.getElementById('country');
    const provinceDropdown = document.getElementById('province');
    const cityDropdown = document.getElementById('city');
    
    // Hidden input fields untuk ID
    const countryIdInput = document.getElementById('country_id');
    const stateIdInput = document.getElementById('state_id');
    const cityIdInput = document.getElementById('city_id');
    
    if (!countryDropdown || !provinceDropdown || !cityDropdown) return;
    
    // Buat container untuk custom input
    const customInputContainer = document.createElement('div');
    customInputContainer.id = 'custom-location-input';
    customInputContainer.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
    customInputContainer.innerHTML = `
        <div class="bg-white rounded-lg p-4 w-11/12 max-w-md">
            <div class="mb-4">
                <h3 class="text-lg font-medium" id="custom-input-title">Enter custom value</h3>
            </div>
            <div class="mb-4">
                <input type="text" id="custom-input-field" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Enter value">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="custom-input-cancel" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700">
                    Cancel
                </button>
                <button type="button" id="custom-input-save" class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium">
                    Save
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(customInputContainer);
    
    // Function to show custom input modal
    function showCustomInput(title, defaultValue, callback) {
        const customInputTitle = document.getElementById('custom-input-title');
        const customInputField = document.getElementById('custom-input-field');
        const customInputCancel = document.getElementById('custom-input-cancel');
        const customInputSave = document.getElementById('custom-input-save');
        
        // Set title and default value
        customInputTitle.textContent = title;
        customInputField.value = defaultValue || '';
        
        // Show modal
        customInputContainer.classList.remove('hidden');
        customInputField.focus();
        
        // Handle cancel button
        customInputCancel.onclick = function() {
            customInputContainer.classList.add('hidden');
            callback(null);
        };
        
        // Handle save button
        customInputSave.onclick = function() {
            const value = customInputField.value.trim();
            if (value) {
                customInputContainer.classList.add('hidden');
                callback(value);
            } else {
                customInputField.classList.add('border-red-500');
                setTimeout(() => customInputField.classList.remove('border-red-500'), 500);
            }
        };
        
        // Handle enter key
        customInputField.onkeydown = function(e) {
            if (e.key === 'Enter') {
                customInputSave.click();
            } else if (e.key === 'Escape') {
                customInputCancel.click();
            }
        };
    }
    
    // Function to load provinces based on country
    function loadProvinces(country) {
        provinceDropdown.innerHTML = '<option value="">Select Province</option>';
        cityDropdown.innerHTML = '<option value="">Select Province First</option>';
        
        if (country && country !== 'other') {
            if (locationData[country] && locationData[country].provinces) {
                locationData[country].provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province;
                    option.textContent = province;
                    
                    // Check for pre-selected values
                    const oldProvince = provinceDropdown.dataset.oldValue;
                    if (province === oldProvince) {
                        option.selected = true;
                    }
                    
                    provinceDropdown.appendChild(option);
                });
            }
            
            // Add "Other" option to province dropdown
            const otherOption = document.createElement('option');
            otherOption.value = 'other';
            otherOption.textContent = 'Other (Enter manually)';
            provinceDropdown.appendChild(otherOption);
            
            // If province is already selected, load cities
            if (provinceDropdown.value && provinceDropdown.value !== 'other') {
                loadCities(country, provinceDropdown.value);
            }
        }
    }
    
    // Function to load cities based on province
    function loadCities(country, province) {
        cityDropdown.innerHTML = '<option value="">Select City</option>';
        
        if (country && province && province !== 'other') {
            if (locationData[country] && 
                locationData[country].cities && locationData[country].cities[province]) {
                
                locationData[country].cities[province].forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    
                    // Check for pre-selected values
                    const oldCity = cityDropdown.dataset.oldValue;
                    if (city === oldCity) {
                        option.selected = true;
                    }
                    
                    cityDropdown.appendChild(option);
                });
            } else {
                // If no predefined cities or if this is the edit page with existing value
                const oldCity = cityDropdown.dataset.oldValue;
                if (oldCity) {
                    const option = document.createElement('option');
                    option.value = oldCity;
                    option.textContent = oldCity;
                    option.selected = true;
                    cityDropdown.appendChild(option);
                }
            }
            
            // Enable direct input if city isn't in the list
            const otherOption = document.createElement('option');
            otherOption.value = 'other';
            otherOption.textContent = 'Other (Enter manually)';
            cityDropdown.appendChild(otherOption);
        }
    }
    
    // Update dropdown with predefined countries and add 'Other' option
    function setupCountryDropdown() {
        // Keep the first empty option
        let firstOption = countryDropdown.querySelector('option:first-child');
        countryDropdown.innerHTML = '';
        countryDropdown.appendChild(firstOption);
        
        // Add countries from location data
        for (const country in locationData) {
            const option = document.createElement('option');
            option.value = country;
            option.textContent = country;
            
            // Check for pre-selected values
            if (country === countryDropdown.dataset.oldValue) {
                option.selected = true;
            }
            
            countryDropdown.appendChild(option);
        }
        
        // Add "Other" option
        const otherOption = document.createElement('option');
        otherOption.value = 'other';
        otherOption.textContent = 'Other (Enter manually)';
        countryDropdown.appendChild(otherOption);
        
        // If country is already selected, load provinces
        if (countryDropdown.value && countryDropdown.value !== 'other') {
            loadProvinces(countryDropdown.value);
        }
    }
    
    // Event listeners for dropdowns
    countryDropdown.addEventListener('change', function() {
        if (this.value === 'other') {
            showCustomInput('Enter country name', '', function(customValue) {
                if (customValue) {
                    // Create new option and select it
                    const option = document.createElement('option');
                    option.value = customValue;
                    option.textContent = customValue;
                    
                    // Remove "other" option temporarily
                    const otherOption = countryDropdown.querySelector('option[value="other"]');
                    countryDropdown.removeChild(otherOption);
                    
                    // Add custom option and select it
                    countryDropdown.appendChild(option);
                    option.selected = true;
                    
                    // Add "other" option back
                    countryDropdown.appendChild(otherOption);
                    
                    // Set custom ID (high number for custom values)
                    if (countryIdInput) countryIdInput.value = '1000';
                    
                    // Clear province and city dropdowns
                    provinceDropdown.innerHTML = '<option value="">Select Province</option>';
                    const provinceOtherOption = document.createElement('option');
                    provinceOtherOption.value = 'other';
                    provinceOtherOption.textContent = 'Other (Enter manually)';
                    provinceDropdown.appendChild(provinceOtherOption);
                    
                    cityDropdown.innerHTML = '<option value="">Select Province First</option>';
                } else {
                    // User cancelled, revert to first option
                    countryDropdown.selectedIndex = 0;
                    if (countryIdInput) countryIdInput.value = '';
                }
            });
        } else if (this.value) {
            // Set the country ID if using hidden fields
            if (countryIdInput) {
                // Get country index (for simple ID generation)
                const countryIndex = Array.from(this.options).findIndex(opt => opt.value === this.value);
                countryIdInput.value = countryIndex > 0 ? countryIndex : '';
            }
            
            loadProvinces(this.value);
        } else {
            // Empty selection
            provinceDropdown.innerHTML = '<option value="">Select Country First</option>';
            cityDropdown.innerHTML = '<option value="">Select Province First</option>';
            if (countryIdInput) countryIdInput.value = '';
        }
    });
    
    provinceDropdown.addEventListener('change', function() {
        if (this.value === 'other') {
            showCustomInput('Enter province name', '', function(customValue) {
                if (customValue) {
                    // Create new option and select it
                    const option = document.createElement('option');
                    option.value = customValue;
                    option.textContent = customValue;
                    
                    // Remove "other" option temporarily
                    const otherOption = provinceDropdown.querySelector('option[value="other"]');
                    provinceDropdown.removeChild(otherOption);
                    
                    // Add custom option and select it
                    provinceDropdown.appendChild(option);
                    option.selected = true;
                    
                    // Add "other" option back
                    provinceDropdown.appendChild(otherOption);
                    
                    // Set custom ID (high number for custom values)
                    if (stateIdInput) stateIdInput.value = '1000';
                    
                    // Update city dropdown
                    cityDropdown.innerHTML = '<option value="">Select City</option>';
                    const cityOtherOption = document.createElement('option');
                    cityOtherOption.value = 'other';
                    cityOtherOption.textContent = 'Other (Enter manually)';
                    cityDropdown.appendChild(cityOtherOption);
                } else {
                    // User cancelled, revert to first option
                    provinceDropdown.selectedIndex = 0;
                    if (stateIdInput) stateIdInput.value = '';
                }
            });
        } else if (countryDropdown.value && this.value) {
            // Set the state ID if using hidden fields
            if (stateIdInput) {
                // Get province index for ID
                const provinceIndex = Array.from(this.options).findIndex(opt => opt.value === this.value);
                stateIdInput.value = provinceIndex > 0 ? provinceIndex : '';
            }
            
            loadCities(countryDropdown.value, this.value);
        } else {
            // Empty selection
            cityDropdown.innerHTML = '<option value="">Select Province First</option>';
            if (stateIdInput) stateIdInput.value = '';
        }
    });
    
    cityDropdown.addEventListener('change', function() {
        if (this.value === 'other') {
            showCustomInput('Enter city name', '', function(customValue) {
                if (customValue) {
                    // Create new option and select it
                    const option = document.createElement('option');
                    option.value = customValue;
                    option.textContent = customValue;
                    
                    // Remove "other" option temporarily
                    const otherOption = cityDropdown.querySelector('option[value="other"]');
                    cityDropdown.removeChild(otherOption);
                    
                    // Add custom option and select it
                    cityDropdown.appendChild(option);
                    option.selected = true;
                    
                    // Add "other" option back
                    cityDropdown.appendChild(otherOption);
                    
                    // Set custom ID (high number for custom values)
                    if (cityIdInput) cityIdInput.value = '1000';
                } else {
                    // User cancelled, revert to first option
                    cityDropdown.selectedIndex = 0;
                    if (cityIdInput) cityIdInput.value = '';
                }
            });
        } else if (this.value) {
            // Set the city ID if using hidden fields
            if (cityIdInput) {
                // Get city index for ID
                const cityIndex = Array.from(this.options).findIndex(opt => opt.value === this.value);
                cityIdInput.value = cityIndex > 0 ? cityIndex : '';
            }
        } else {
            // Empty selection
            if (cityIdInput) cityIdInput.value = '';
        }
    });
    
    // Initialize on page load
    setupCountryDropdown();
    
    // If country already has value on page load, update province and city
    if (countryDropdown.value && countryDropdown.value !== 'other') {
        loadProvinces(countryDropdown.value);
        
        // If province already has value, update city
        if (provinceDropdown.value && provinceDropdown.value !== 'other') {
            loadCities(countryDropdown.value, provinceDropdown.value);
        }
    }
}); 