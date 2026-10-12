@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h2 mb-1">
            <i class="fas fa-wallet text-warning me-2"></i>Address Book & Payment Vault
        </h1>
        <p class="text-muted mb-0">Manage your verified shipping addresses and saved payment methods</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAddressModal">
            <i class="fas fa-plus-circle me-1"></i> Add New Address
        </button>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#newPaymentModal">
            <i class="fas fa-credit-card me-1"></i> Save Payment Method
        </button>
    </div>
</div>

<div class="row">
    <!-- 📍 ADDRESS BOOK MANAGER -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-map-marked-alt text-primary me-2"></i> Delivery Address Book
                </h5>
                <span class="badge bg-primary rounded-pill">{{ $addresses->count() }} Saved</span>
            </div>
            <div class="card-body">
                @forelse($addresses as $addr)
                <div class="card mb-3 border {{ $addr->is_default ? 'border-primary shadow-sm bg-light-subtle' : 'border-light-subtle' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge {{ $addr->title == 'Home' ? 'bg-success' : ($addr->title == 'Office' ? 'bg-info' : 'bg-secondary') }} me-2">
                                    <i class="fas {{ $addr->title == 'Home' ? 'fa-home' : 'fa-building' }} me-1"></i> {{ $addr->title }}
                                </span>
                                @if($addr->is_default)
                                    <span class="badge bg-primary"><i class="fas fa-check-circle me-1"></i> Default Shipping Address</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                @if(!$addr->is_default)
                                <form method="POST" action="{{ route('vault.addresses.default', $addr->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-primary" title="Set as default">Set Default</button>
                                </form>
                                @endif

                                <form method="POST" action="{{ route('vault.addresses.destroy', $addr->id) }}" onsubmit="return confirm('Remove this address?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete address">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-1">{{ $addr->recipient_name }}</h6>
                        <p class="text-muted small mb-1">
                            {{ $addr->address_line1 }}{{ $addr->address_line2 ? ', ' . $addr->address_line2 : '' }}<br>
                            {{ $addr->city }}, {{ $addr->state }} - {{ $addr->zip_code }}, {{ $addr->country }}
                        </p>
                        <small class="text-muted"><i class="fas fa-phone me-1"></i> {{ $addr->phone }}</small>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No addresses saved yet. Add your home or office address for faster checkout!</p>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newAddressModal">
                        + Add First Address
                    </button>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 💳 SAVED PAYMENT METHODS VAULT -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-shield-alt text-success me-2"></i> Saved Payment Methods
                </h5>
                <span class="badge bg-success rounded-pill">{{ $paymentMethods->count() }} Active</span>
            </div>
            <div class="card-body">
                @forelse($paymentMethods as $pm)
                <div class="card mb-3 border {{ $pm->is_default ? 'border-success shadow-sm bg-light-subtle' : 'border-light-subtle' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @if($pm->method_type === 'card')
                                    <div class="me-3 fs-2 text-primary">
                                        <i class="fab fa-cc-{{ strtolower($pm->card_brand) == 'mastercard' ? 'mastercard' : 'visa' }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $pm->card_brand }} •••• {{ $pm->last_four }}</h6>
                                        <small class="text-muted">Exp: {{ $pm->expiry_month }}/{{ $pm->expiry_year }} | {{ $pm->holder_name }}</small>
                                    </div>
                                @else
                                    <div class="me-3 fs-2 text-success">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">UPI ID</h6>
                                        <small class="text-muted font-monospace">{{ $pm->upi_id }}</small>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                @if(!$pm->is_default)
                                <form method="POST" action="{{ route('vault.payments.default', $pm->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-success" title="Set as default">Set Default</button>
                                </form>
                                @else
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Default</span>
                                @endif

                                <form method="POST" action="{{ route('vault.payments.destroy', $pm->id) }}" onsubmit="return confirm('Remove payment shortcut?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No saved payment shortcuts. Store your credit/debit card or UPI ID for seamless 1-click reorders.</p>
                    <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#newPaymentModal">
                        + Save Payment Method
                    </button>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- 📍 MODAL: ADD NEW ADDRESS -->
<div class="modal fade" id="newAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2"></i>Add Delivery Address</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('vault.addresses.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Address Tag</label>
                            <select name="title" class="form-select" required>
                                <option value="Home">Home</option>
                                <option value="Office">Office</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Recipient Full Name</label>
                            <input type="text" name="recipient_name" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Contact Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Street Address Line 1</label>
                            <input type="text" name="address_line1" class="form-control" placeholder="House / Flat No., Building Name, Street" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Address Line 2 (Optional)</label>
                            <input type="text" name="address_line2" class="form-control" placeholder="Landmark, Area">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">City</label>
                            <input type="text" name="city" class="form-control" placeholder="Ahmedabad" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">State</label>
                            <input type="text" name="state" class="form-control" placeholder="Gujarat" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Postal Code</label>
                            <input type="text" name="zip_code" class="form-control" placeholder="380001" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="defAddr">
                                <label class="form-check-label small" for="defAddr">
                                    Set as default shipping address
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 💳 MODAL: ADD PAYMENT METHOD -->
<div class="modal fade" id="newPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-lock me-2 text-warning"></i>Save Payment Shortcut</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('vault.payments.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Method Type</label>
                        <select name="method_type" id="pmTypeSelect" class="form-select" onchange="togglePaymentInputs(this.value)" required>
                            <option value="card">Credit / Debit Card</option>
                            <option value="upi">UPI ID (Google Pay, PhonePe, Paytm)</option>
                        </select>
                    </div>

                    <!-- Card Inputs -->
                    <div id="cardFields">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Card Number</label>
                            <input type="text" name="card_number" class="form-control" placeholder="4532 •••• •••• 8821">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Cardholder Name</label>
                            <input type="text" name="holder_name" class="form-control" value="{{ Auth::user()->name }}">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Expiry (MM/YY)</label>
                                <input type="text" name="expiry" class="form-control" placeholder="12/28">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">CVV (Masked)</label>
                                <input type="password" class="form-control" placeholder="•••" maxlength="4">
                            </div>
                        </div>
                    </div>

                    <!-- UPI Inputs -->
                    <div id="upiFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">UPI ID (VPA)</label>
                            <input type="text" name="upi_id" class="form-control" placeholder="mobile@upi / name@okhdfcbank">
                        </div>
                    </div>

                    <div class="alert alert-secondary small mb-0">
                        <i class="fas fa-shield-alt text-success me-1"></i> Card details are tokenized & stored securely in compliance with PCI-DSS standards.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save to Vault</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePaymentInputs(type) {
        if (type === 'upi') {
            document.getElementById('cardFields').style.display = 'none';
            document.getElementById('upiFields').style.display = 'block';
        } else {
            document.getElementById('cardFields').style.display = 'block';
            document.getElementById('upiFields').style.display = 'none';
        }
    }
</script>
@endsection
