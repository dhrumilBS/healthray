<div class="section-wrapper">
    <div class="content-sec">
        <h2>Create Ayushman Bharat Health Account ABHA (Health ID) Card</h2>
    </div>

    <div class="form-container">
        <div id="common-section">
            <div class="link-action">
                <button class="link-action-btn active" data-view="aadhaar">Aadhaar Number</button>
                <button class="link-action-btn" data-view="mobile">Mobile Number</button>
            </div>

            <!-- auth-form - aadhaar -->
            <form method="POST" class="auth-form active" data-type="aadhaar" data-url="/v2/abha/m1-external/aadhaar/generate_otp">
                <input type="hidden" name="action" value="aadhaar_auth_form_submit">
                <div class="form-field">
                    <input type="text" class="input-field auth-input" id="aadhaar-input" pattern="[0-9]{12}" placeholder="Enter Aadhaar Number" required>
                </div>
                <div class="checkbox-input">
                    <input type="checkbox" id="Aadhaar-permissions" required>
                    <label for="Aadhaar-permissions">I agree to the necessary permissions for Healthray to set up my ABHA Health Locker.</label>
                </div>
                <div class="form-submit">
                    <button type="submit" class="submit-btn">Submit Aadhaar</button>
                </div>
            </form>

            <!-- auth-form - mobile -->
            <form method="POST" class="auth-form" data-type="mobile" data-url="/v1/abha/m1-external/phr/generate_otp">
                <input type="hidden" name="action" value="mobile_auth_form_submit">
                <div class="form-field">
                    <input type="text" class="input-field auth-input" id="mobile-input" pattern="[0-9]{10}" placeholder="Enter Mobile Number" required>
                </div>
                <div class="checkbox-input">
                    <input type="checkbox" id="mobile-permissions" required>
                    <label for="mobile-permissions">I agree to the necessary permissions for Healthray to set up my ABHA Health Locker.</label>
                </div>
                <div class="form-submit">
                    <button type="submit" class="submit-btn">Submit Mobile</button>
                </div>
            </form>

            <input type="hidden" name="transaction_id" id="transaction-id">
        </div>

        <!-- adhar-otp -->
        <div id="adhar-otp-section" class="otp-section">
            <form id="adhar-otp-form" class="otp-form" data-type="aadhaar" data-url="/v2/abha/m1-external/aadhaar/verify_otp">
                <input type="hidden" name="action" value="verify_otp">

                <div class="form-field d-flex">
                    <input type="text" id="adhar-otp-input" class="input-field auth-input otp-input" placeholder="Enter OTP" maxlength="6" required>
                    <div class="form-submit resend-btn">
                        <button class="otp-resend-btn submit-link" disabled><span> Resend OTP</span></button>
                        <button class="change-mobile submit-link">Change Mobile</button>
                    </div>
                </div>

                <div class="second-mobile-number">
                    <div class="form-field">
                        <input type="tel" id="adhar-otp-mobile-input" name="otp-mobile" class="input-field auth-input" pattern="[0-9]{10}" placeholder="Enter your mobile number" required>
                    </div>
                    <div class="d-flex info-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0 6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z"></path>
                        </svg>
                        <div>This mobile number will be used for all the communications</div>
                    </div>
                </div>

                <div class="form-submit">
                    <button type="submit" class="submit-btn">Verify OTP</button>
                </div>
            </form>
        </div>

        <!-- mobile-otp -->
        <div id="mobile-otp-section" class="otp-section">
            <form method="POST" id="mobile-otp-form" class="otp-form" data-type="mobile" data-url="/v1/abha/m1-external/phr/verify_otp">
                <input type="hidden" name="action" value="verify_otp">
                <div class="form-field d-flex">
                    <input type="text" id="mobile-otp-input" name="otp-input" class="input-field auth-input otp-input" placeholder="Enter OTP" maxlength="6" required>
                    <div class="form-submit resend-btn">
                        <button class="otp-resend-btn submit-link" disabled><span> Resend OTP</span></button>
                        <button class="change-mobile submit-link">Change Mobile</button>
                    </div>
                </div>

                <div class="form-submit">
                    <button type="submit" class="submit-btn">Verify OTP</button>
                </div>
            </form>
        </div>

        <!-- adhar-mobile-otp -->
        <div id="adhar-mobile-otp-section" class="adhar-mobile-otp-section otp-section">
            <form method="POST" id="adhar-mobile-otp-form" class="adhar-mobile-otp-form otp-form" data-type="aadhaar-mobile" data-url="/v2/abha/m1-external/mobile/verify_otp">
                <input type="hidden" name="action" value="verify_otp">
                <div class="form-field d-flex">
                    <input type="text" id="adhar-mobile-otp-input" name="otp-input" class="input-field auth-input otp-input" placeholder="Enter OTP" maxlength="6" required>
                    <div class="form-submit resend-btn">
                        <button class="otp-resend-btn submit-link" disabled><span> Resend OTP</span></button>
                        <button class="change-mobile submit-link">Change Mobile</button>
                    </div>
                </div>

                <div class="form-submit">
                    <button type="submit" class="submit-btn">Verify OTP</button>
                </div>
            </form>
        </div>

        <!-- Select ABHA to continue -->
        <div class="choose-abha-section">
            <div class="text-center header">
                <h3 class="title">Select ABHA to continue</h3>
                <p class="founded-address">We have found ABHA 1 address linked with your mobile number.</p>
            </div>
            <div class="abha-phr-address">
                <div class="abha-list form-field"> </div>
            </div>
            <div class="separator">
                <span>or</span>
            </div>
            <div class="create-abha text-center">
                <button class="btn" id="create-abha-btn">
                    <div class="d-flex">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                            <path d="M18.5 12c0 .563-.469 1.031-1 1.031H13v4.5c0 .532-.469.969-1 .969a.98.98 0 01-1-.969v-4.5H6.5c-.563 0-1-.469-1-1.031a.98.98 0 011-.969H11v-4.5c0-.562.438-1.031 1-1.031.531 0 1 .469 1 1.031v4.5h4.5c.531-.031 1 .438 1 .969z" fill="currentColor"></path>
                        </svg>
                        <p>Create new ABHA address</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- CREATE NEW ABHA ADDRESS -->
        <div class="create-abha-section">
            <form id="create-abha-form" class="create-abha-form">
                <div class="heading text-center">
                    <h3>Enter ABHA Information</h3>
                </div>
                <div class="form-content">
                    <div class="form-field">
                        <div class="form-group full-name">
                            <div>
                                <input type="text" id="fname" placeholder="First Name" class="input-field">
                            </div>
                            <div>
                                <input type="text" id="mname" placeholder="Middle Name" class="input-field">
                            </div>
                            <div>
                                <input type="text" id="lname" placeholder="Last Name" class="input-field">
                            </div>
                        </div>
                    </div>
                    <div class="form-field">
                        <p>Date of Birth<span class="required">*</span></p>
                        <div class="form-group date-of-birth">
                            <div>
                                <select id="day" class="input-field">
                                    <option value="">Day</option>
                                </select>
                            </div>
                            <div>
                                <select id="month" class="input-field">
                                    <option value="">Month</option>
                                </select>
                            </div>
                            <div>
                                <select id="year" class="input-field">
                                    <option value="">Year</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-field">
                        <p>Gender<span class="required">*</span></p>
                        <div class="form-group radio-input gender-options">
                            <div class="">
                                <input type="radio" name="gender" id="male" value="male" class="input-field">
                                <label for="male"> Male </label>
                            </div>
                            <div class="">
                                <input type="radio" name="gender" id="female" value="female" class="input-field">
                                <label for="female">Female </label>
                            </div>
                            <div class="">
                                <input type="radio" name="gender" id="other" value="other" class="input-field">
                                <label for="other">Other </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-field">
                        <div class="form-group state-city">
                            <div>
                                <label for="state">State<span class="required">*</span></label>
                                <select name="state" id="state" class="input-field">
                                    <option value="">Select a State</option>
                                </select>
                            </div>
                            <div>
                                <label for="district">District<span class="required">*</span></label>
                                <select name="district" id="district" class="input-field">
                                    <option value="">Select a District</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-field">
                        <p>Pincode<span class="required">*</span></p>
                        <input type="number" id="pincode" pattern="{0-9}[6]" placeholder="Pin code" class="input-field">
                    </div>
                    <div class="form-field">
                        <p>address<span class="required">*</span></p>
                        <textarea id="address" placeholder="Pin code" class="input-field"></textarea>
                    </div>
                </div>
                <div class="form-submit">
                    <button type="submit" class="submit-btn">Create New Address</button>
                </div>
            </form>
        </div>

        <!-- Suggestion Form -->
        <div class="suggestion-section">
            <div class="text-center header">
                <h3 class="title">Suggested Addresses</h3>

            </div>

            <div class="suggestion-list-wrap no-data">
                <div class="suggestion-list"> No Data </div>
                <div class="separator"> <span>Or create your Custom ABHA Address</span> </div>
                <form class="abha-address-form">
                    <div class="form-field">
                        <input type="hidden" id="abha-address-type" name="abha-address-type" class="input-field" required disabled>
                        <input type="text" id="abha-address-input" name="abha-address" class="input-field abha-address-field" placeholder="Enter custom ABHA Address" required disabled>
                        <div class="note"><em>Note: Use Alphabets[a-z, A-Z], Numbers[0-9], or special characters (_,.)</em></div>
                    </div>
                    <input role="button" type="submit" class="btn" id="submitSuggestion" value="Submit Suggestion">
                </form>
                <button role="button" class="btn " id="getaadharmobilesuggestion"> Get Suggestion</button>
                <button role="button" class="btn " id="getsuggestion"> get suggestion </button>
            </div>
        </div>
    </div>

    <div id="response-message">

    </div>
    <div id="loading-spinner" style="display: none;">Loading...</div>
</div>