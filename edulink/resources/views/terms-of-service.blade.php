@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body p-5">
                    <h1 class="mb-4">Terms of Service</h1>
                    <p class="text-muted mb-4">Last updated: {{ date('F d, Y') }}</p>

                    <section class="mb-4">
                        <h3>1. Acceptance of Terms</h3>
                        <p>By accessing and using the Edulink SmartFees system, you accept and agree to be bound by the terms and provision of this agreement.</p>
                    </section>

                    <section class="mb-4">
                        <h3>2. Use License</h3>
                        <p>Permission is granted to temporarily access the materials on Edulink SmartFees for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
                        <ul>
                            <li>Modify or copy the materials</li>
                            <li>Use the materials for any commercial purpose</li>
                            <li>Attempt to reverse engineer any software</li>
                            <li>Remove any copyright or other proprietary notations</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h3>3. User Accounts</h3>
                        <p>When you create an account with us, you must provide information that is accurate, complete, and current at all times. You are responsible for safeguarding the password and for all activities under your account.</p>
                    </section>

                    <section class="mb-4">
                        <h3>4. Payment Terms</h3>
                        <p>All fees are due as specified in your enrollment agreement. Late payments may incur additional charges. We reserve the right to suspend services for non-payment.</p>
                    </section>

                    <section class="mb-4">
                        <h3>5. Prohibited Uses</h3>
                        <p>You may not use our service:</p>
                        <ul>
                            <li>For any unlawful purpose or to solicit others to unlawful acts</li>
                            <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
                            <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
                            <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
                            <li>To submit false or misleading information</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h3>6. Service Availability</h3>
                        <p>We strive to maintain system availability but do not guarantee uninterrupted access. We may suspend the service for maintenance or updates.</p>
                    </section>

                    <section class="mb-4">
                        <h3>7. Limitation of Liability</h3>
                        <p>In no event shall Edulink International College Nairobi or its suppliers be liable for any damages arising out of the use or inability to use the materials on this system.</p>
                    </section>

                    <section class="mb-4">
                        <h3>8. Changes to Terms</h3>
                        <p>We reserve the right to revise these terms of service at any time without notice. By using this system, you are agreeing to be bound by the current version of these terms.</p>
                    </section>

                    <section class="mb-4">
                        <h3>9. Contact Information</h3>
                        <p>If you have any questions about these Terms of Service, please contact us at:</p>
                        <p>
                            <strong>Edulink International College Nairobi</strong><br>
                            Email: legal@edulink.ac.ke<br>
                            Phone: +254700000000
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection