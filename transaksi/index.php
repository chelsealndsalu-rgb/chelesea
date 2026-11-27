
<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

require_login();

/*
|--------------------------------------------------------------------------
| Mengambil data pelanggan
|--------------------------------------------------------------------------
*/

$customerResult = $conn->query(
    "SELECT id_pelanggan, nama, nomor_telepon
     FROM pelanggan
     ORDER BY nama ASC"
);

$customers = [];

while ($row = $customerResult->fetch_assoc()) {
    $customers[] = $row;
}

/*
|--------------------------------------------------------------------------
| Mengambil data layanan
|--------------------------------------------------------------------------
*/

$serviceResult = $conn->query(
    "SELECT id_layanan, nama_layanan, harga_per_kg, durasi_jam
     FROM layanan
     ORDER BY id_layanan ASC"
);

$services = [];

while ($row = $serviceResult->fetch_assoc()) {
    $services[] = $row;
}

$pageTitle = 'Transaksi Baru';
$active = 'transaksi';

include __DIR__ . '/../includes/header.php';

?>

<div class="transaction-page">

    <div class="transaction-heading">
        <h1>Transaksi Baru</h1>
    </div>

    <form
        method="POST"
        action="simpan.php"
        id="transactionForm"
    >

        <div class="transaction-layout">

            <!-- KOLOM KIRI -->
            <div class="transaction-main">

                <!-- PILIH PELANGGAN -->
                <section class="transaction-card">

                    <h2>Pilih Pelanggan</h2>

                    <button type="button" class="btn-add-customer" id="openCustomerModal">
    + Tambah Pelanggan Baru
</button>

                    <div class="customer-autocomplete">

                        <div class="transaction-search">

                            <span class="search-symbol">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="text"
                                id="customerSearch"
                                class="form-control"
                                placeholder="Ketik nama atau nomor telepon pelanggan..."
                                autocomplete="off"
                            >

                        </div>

                        <input
                            type="hidden"
                            name="id_pelanggan"
                            id="customerId"
                            required
                        >

                        <div
                            class="customer-suggestions"
                            id="customerSuggestions"
                        ></div>

                    </div>

                </section>


                <!-- DETAIL LAUNDRY -->
                <section class="transaction-card">

                    <h2>Detail Laundry</h2>

                    <!-- BERAT PAKAIAN -->
                    <div class="transaction-field">

                        <label for="weightInput" class="form-label">
                            Berat Pakaian (kg)
                            <span class="required-mark">*</span>
                        </label>

                        <div class="weight-input-group">

                            <input
                                type="number"
                                class="form-control"
                                name="berat_pakaian"
                                id="weightInput"
                                min="0.01"
                                step="0.01"
                                placeholder="Contoh: 3.5"
                                inputmode="decimal"
                                required
                            >

                            <span>kg</span>

                        </div>

                    </div>


                    <!-- JENIS LAYANAN -->
                    <div class="transaction-field">

                        <label class="form-label">
                            Jenis Layanan
                            <span class="required-mark">*</span>
                        </label>

                        <div class="service-options">

                            <?php foreach ($services as $index => $service): ?>

                                <?php
                                $duration = (int) $service['durasi_jam'];

                                if ($duration <= 0) {
                                    $durationText = '-';
                                } elseif ($duration % 24 === 0) {
                                    $durationText = ($duration / 24) . ' hari';
                                } else {
                                    $durationText = $duration . ' jam';
                                }
                                ?>

                                <label class="service-option">

                                    <input
                                        type="radio"
                                        name="service_choice"
                                        class="service-radio"
                                        value="<?= (int) $service['id_layanan'] ?>"
                                        data-name="<?= e($service['nama_layanan']) ?>"
                                        data-price="<?= (float) $service['harga_per_kg'] ?>"
                                        data-duration="<?= $duration ?>"
                                        <?= $index === 0 ? 'checked' : '' ?>
                                    >

                                    <span class="service-option-content">

                                        <span class="service-option-name">
                                            <?= e($service['nama_layanan']) ?>
                                        </span>

                                        <strong class="service-option-price">
                                            Rp<?= number_format((float) $service['harga_per_kg'], 0, ',', '.') ?><small>/kg</small>
                                        </strong>

                                        <span class="service-option-duration">
                                            <i class="bi bi-clock"></i>
                                            <?= e($durationText) ?>
                                        </span>

                                    </span>

                                    <span class="service-check">
                                        <i class="bi bi-check"></i>
                                    </span>

                                </label>

                            <?php endforeach; ?>

                        </div>

                        <input
                            type="hidden"
                            name="id_layanan"
                            id="serviceSelect"
                            value="<?= isset($services[0]) ? (int) $services[0]['id_layanan'] : '' ?>"
                            required
                        >

                    </div>

                </section>


                <!-- METODE PEMBAYARAN -->
                <section class="transaction-card">

                    <h2>Metode Pembayaran</h2>

                    <input
                        type="hidden"
                        name="metode_pembayaran"
                        id="paymentMethod"
                        value="Tunai"
                        required
                    >

                    <div class="payment-buttons">

                        <button
                            type="button"
                            class="payment-button active"
                            data-method="Tunai"
                        >
                            Tunai
                        </button>

                        <button
                            type="button"
                            class="payment-button"
                            data-method="QRIS"
                        >
                            QRIS
                        </button>

                    </div>

                </section>

            </div>


            <!-- KOLOM KANAN -->
            <aside class="transaction-summary">

                <section class="transaction-card summary-card">

                    <h2>Ringkasan Biaya</h2>

                    <div class="summary-details">

                        <div class="summary-row">
                            <span>Pelanggan</span>
                            <strong id="summaryCustomer">
                                Belum dipilih
                            </strong>
                        </div>

                        <div class="summary-row">
                            <span>Jenis Layanan</span>
                            <strong id="summaryService">
                                <?= isset($services[0]) ? e($services[0]['nama_layanan']) : '-' ?>
                            </strong>
                        </div>

                        <div class="summary-row">
                            <span>Berat Pakaian</span>
                            <strong id="summaryWeight">0 kg</strong>
                        </div>

                        <div class="summary-row">
                            <span>Harga per Kg</span>
                            <strong id="summaryPrice">
                                <?= isset($services[0])
                                    ? 'Rp' . number_format((float) $services[0]['harga_per_kg'], 0, ',', '.') . '/kg'
                                    : 'Rp0/kg' ?>
                            </strong>
                        </div>

                        <div class="summary-row">
                            <span>Metode Pembayaran</span>
                            <strong id="summaryPayment">Tunai</strong>
                        </div>

                        <div class="summary-row summary-estimate">
                            <span>Estimasi Selesai</span>
                            <strong id="summaryEstimate">-</strong>
                        </div>

                    </div>

                    <div class="summary-total">
                        <strong id="totalPreview">Rp0</strong>
                    </div>

                    <button
                        type="submit"
                        class="btn-create-invoice"
                        <?= empty($services) ? 'disabled' : '' ?>
                    >
                        Buat Nota
                    </button>

                </section>

            </aside>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================
       DATA DAN ELEMENT
    ===================================== */

    const customers = <?= json_encode(
        array_map(function ($customer) {
            return [
                'id' => (int) $customer['id_pelanggan'],
                'nama' => $customer['nama'],
                'telepon' => $customer['nomor_telepon']
            ];
        }, $customers),
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    ) ?>;

    const customerSearch = document.getElementById('customerSearch');
    const customerId = document.getElementById('customerId');
    const customerSuggestions = document.getElementById('customerSuggestions');

    const weightInput = document.getElementById('weightInput');

    const serviceRadios = document.querySelectorAll('.service-radio');
    const serviceSelect = document.getElementById('serviceSelect');

    const paymentButtons = document.querySelectorAll('.payment-button');
    const paymentMethod = document.getElementById('paymentMethod');

    const summaryCustomer = document.getElementById('summaryCustomer');
    const summaryService = document.getElementById('summaryService');
    const summaryWeight = document.getElementById('summaryWeight');
    const summaryPrice = document.getElementById('summaryPrice');
    const summaryPayment = document.getElementById('summaryPayment');
    const summaryEstimate = document.getElementById('summaryEstimate');
    const totalPreview = document.getElementById('totalPreview');


    /* =====================================
       FORMAT RUPIAH
    ===================================== */

    function formatRupiah(value) {

        return 'Rp' + new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 0
        }).format(value);

    }


    /* =====================================
       AUTOCOMPLETE PELANGGAN
    ===================================== */

    
/* =====================================
   AUTOCOMPLETE PELANGGAN + KEYBOARD
===================================== */

let activeCustomerIndex = -1;
let filteredCustomers = [];

function showCustomerSuggestions(keyword) {

    customerSuggestions.innerHTML = '';
    activeCustomerIndex = -1;

    if (keyword.length === 0) {
        customerSuggestions.style.display = 'none';
        return;
    }

    filteredCustomers = customers.filter(customer => {

        const nama = customer.nama.toLowerCase();
        const telepon = String(customer.telepon).toLowerCase();

        return nama.includes(keyword) || telepon.includes(keyword);

    });

    if (filteredCustomers.length === 0) {

        const empty = document.createElement('div');

        empty.className = 'customer-no-result';
        empty.textContent = 'Pelanggan tidak ditemukan';

        customerSuggestions.appendChild(empty);
        customerSuggestions.style.display = 'block';

        return;
    }

    filteredCustomers.forEach((customer, index) => {

        const item = document.createElement('button');

        item.type = 'button';
        item.className = 'customer-suggestion-item';

        const name = document.createElement('strong');
        name.textContent = customer.nama;

        const phone = document.createElement('span');
        phone.textContent = customer.telepon;

        item.appendChild(name);
        item.appendChild(phone);

        item.addEventListener('click', function () {
            selectCustomer(customer);
        });

        customerSuggestions.appendChild(item);

    });

    customerSuggestions.style.display = 'block';

}

/* =====================================
   PILIH PELANGGAN
===================================== */

function selectCustomer(customer) {

    customerSearch.value = customer.nama;
    customerId.value = customer.id;

    customerSuggestions.style.display = 'none';
    activeCustomerIndex = -1;

    updateSummary();

}

/* =====================================
   NAVIGASI KEYBOARD
===================================== */

customerSearch.addEventListener('keydown', function (event) {

    const items = customerSuggestions.querySelectorAll(
        '.customer-suggestion-item'
    );

    if (customerSuggestions.style.display !== 'block') {
        return;
    }

    if (event.key === 'ArrowDown') {

        event.preventDefault();

        if (items.length === 0) return;

        activeCustomerIndex++;

        if (activeCustomerIndex >= items.length) {
            activeCustomerIndex = 0;
        }

        updateCustomerHighlight(items);

    }

    else if (event.key === 'ArrowUp') {

        event.preventDefault();

        if (items.length === 0) return;

        activeCustomerIndex--;

        if (activeCustomerIndex < 0) {
            activeCustomerIndex = items.length - 1;
        }

        updateCustomerHighlight(items);

    }

    else if (event.key === 'Enter') {

        if (activeCustomerIndex >= 0 && items[activeCustomerIndex]) {

            event.preventDefault();

            items[activeCustomerIndex].click();

        }

    }

    else if (event.key === 'Escape') {

        customerSuggestions.style.display = 'none';
        activeCustomerIndex = -1;

    }

});

/* =====================================
   HIGHLIGHT REKOMENDASI
===================================== */

function updateCustomerHighlight(items) {

    items.forEach((item, index) => {

        item.classList.toggle(
            'active',
            index === activeCustomerIndex
        );

    });

    if (items[activeCustomerIndex]) {

        items[activeCustomerIndex].scrollIntoView({
            block: 'nearest'
        });

    }

}



    customerSearch.addEventListener('input', function () {

        const keyword = this.value.trim().toLowerCase();

        // Hapus pilihan sebelumnya jika pencarian berubah
        customerId.value = '';

        updateSummary();

        showCustomerSuggestions(keyword);

    });


    // Menutup rekomendasi jika klik di luar area pencarian
    document.addEventListener('click', function (event) {

        if (!event.target.closest('.customer-autocomplete')) {
            customerSuggestions.style.display = 'none';
        }

    });


    /* =====================================
       PILIH JENIS LAYANAN
    ===================================== */

    serviceRadios.forEach(radio => {

        radio.addEventListener('change', function () {

            serviceSelect.value = this.value;

            updateSummary();

        });

    });


    /* =====================================
       PILIH METODE PEMBAYARAN
    ===================================== */

    paymentButtons.forEach(button => {

        button.addEventListener('click', function () {

            paymentButtons.forEach(item => {
                item.classList.remove('active');
            });

            this.classList.add('active');

            paymentMethod.value = this.dataset.method;

            updateSummary();

        });

    });


    /* =====================================
       UPDATE RINGKASAN BIAYA
    ===================================== */

    function updateSummary() {

        const selectedCustomer = customers.find(
            customer => customer.id == customerId.value
        );

        summaryCustomer.textContent = selectedCustomer
            ? selectedCustomer.nama
            : 'Belum dipilih';


        const selectedService = document.querySelector(
            '.service-radio:checked'
        );

        const weight = parseFloat(weightInput.value) || 0;


        if (selectedService) {

            const price = parseFloat(selectedService.dataset.price) || 0;
            const duration = parseInt(selectedService.dataset.duration, 10);

            summaryService.textContent = selectedService.dataset.name;

            summaryPrice.textContent = formatRupiah(price) + '/kg';


            // Estimasi selesai
            if (!Number.isFinite(duration) || duration <= 0) {

                summaryEstimate.textContent = '-';

            } else if (duration % 24 === 0) {

                summaryEstimate.textContent = (duration / 24) + ' hari';

            } else {

                summaryEstimate.textContent = duration + ' jam';

            }


            // Total pembayaran
            totalPreview.textContent = formatRupiah(weight * price);

        } else {

            summaryService.textContent = '-';
            summaryPrice.textContent = 'Rp0/kg';
            summaryEstimate.textContent = '-';
            totalPreview.textContent = 'Rp0';

        }


        summaryWeight.textContent =
            weight.toLocaleString('id-ID') + ' kg';

        summaryPayment.textContent = paymentMethod.value;

    }


    /* =====================================
       EVENT INPUT
    ===================================== */

    weightInput.addEventListener('input', updateSummary);


    /* =====================================
       VALIDASI FORM
    ===================================== */

    document.getElementById('transactionForm').addEventListener('submit', function (event) {

        if (!customerId.value) {

            event.preventDefault();

            alert('Silakan pilih pelanggan dari rekomendasi.');

            customerSearch.focus();

            return;

        }

        if (!serviceSelect.value) {

            event.preventDefault();

            alert('Silakan pilih jenis layanan.');

            return;

        }

        if (!weightInput.value || parseFloat(weightInput.value) <= 0) {

            event.preventDefault();

            alert('Masukkan berat pakaian yang valid.');

            weightInput.focus();

            return;

        }

        if (!paymentMethod.value) {

            event.preventDefault();

            alert('Silakan pilih metode pembayaran.');

            return;

        }

    });


    // Ringkasan awal
    updateSummary();

});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
