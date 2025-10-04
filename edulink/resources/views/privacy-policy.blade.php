@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body p-5">
                    <h1 class="mb-4">Privacy Policy</h1>
                    <p class="text-muted mb-4">Last updated: {{ date('F d, Y') }}</p>

                    <section class="mb-4">
                        <h3>1. Information We Collect</h3>
                        <p>We collect information you provide directly to us, such as when you create an account, make payments, or contact us for support.</p>
                        <ul>
                            <li>Personal information (name, email, phone number)</li>
                            <li>Academic information (student ID, course enrollment)</li>
                            <li>Payment information (transaction details, payment methods)</li>
                            <li>Usage data (login times, system interactions)</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h3>2. How We Use Your Information</h3>
                        <p>We use the information we collect to:</p>
                        <ul>
                            <li>Provide and maintain our fee management services</li>
                            <li>Process payments and manage your account</li>
                            <li>Send important notifications about your account</li>
                            <li>Improve our services and user experience</li>
                            <li>Comply with legal obligations</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h3>3. Information Sharing</h3>
                        <p>We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:</p>
                        <ul>
                            <li>With your consent</li>
                            <li>To comply with legal requirements</li>
                            <li>With service providers who assist in our operations</li>
                            <li>To protect our rights and safety</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h3>4. Data Security</h3>
                        <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                    </section>

                    <section class="mb-4">
                        <h3>5. Your Rights</h3>
                        <p>You have the right to:</p>
                        <ul>
                            <li>Access your personal information</li>
                            <li>Correct inaccurate information</li>
                            <li>Request deletion of your information</li>
                            <li>Object to processing of your information</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h3>6. Contact Us</h3>
                        <p>If you have questions about this Privacy Policy, please contact us at:</p>
                        <p>
                            <strong>Edulink International College Nairobi</strong><br>
                            Email: privacy@edulink.ac.ke<br>
                            Phone: +254700000000
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection