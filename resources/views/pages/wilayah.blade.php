@extends('layouts.main')

@section('content')
<div class="page-header">
	<h3 class="page-title">Pilih Wilayah</h3>
</div>

<div class="row">
	<div class="col-lg-8 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Form Wilayah Indonesia</h4>
				<p class="mb-4 card-description">Pilih data wilayah secara berurutan dari provinsi sampai kelurahan.</p>

				<div class="mb-3 form-group">
					<label for="provinsi">Provinsi</label>
					<select id="provinsi" class="form-control">
						<option value="0">Memuat data provinsi...</option>
					</select>
				</div>

				<div class="mb-3 form-group">
					<label for="kota">Kota/Kabupaten</label>
					<select id="kota" class="form-control" disabled>
						<option value="0">Pilih Kota</option>
					</select>
				</div>

				<div class="mb-3 form-group">
					<label for="kecamatan">Kecamatan</label>
					<select id="kecamatan" class="form-control" disabled>
						<option value="0">Pilih Kecamatan</option>
					</select>
				</div>

				<div class="mb-0 form-group">
					<label for="kelurahan">Kelurahan</label>
					<select id="kelurahan" class="form-control" disabled>
						<option value="0">Pilih Kelurahan</option>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-4 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Ringkasan Pilihan</h4>
				<div class="gap-2 d-flex flex-column">
					<div>
						<small class="text-muted d-block mb-2">Provinsi</small>
						<span id="labelProvinsi" class="badge badge-outline-primary">-</span>
					</div>
					<div>
						<small class="text-muted d-block mb-2">Kota/Kabupaten</small>
						<span id="labelKota" class="badge badge-outline-info">-</span>
					</div>
					<div>
						<small class="text-muted d-block mb-2">Kecamatan</small>
						<span id="labelKecamatan" class="badge badge-outline-warning">-</span>
					</div>
					<div>
						<small class="text-muted d-block mb-2">Kelurahan</small>
						<span id="labelKelurahan" class="badge badge-outline-success">-</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
	const provinsi = document.getElementById('provinsi');
	const kota = document.getElementById('kota');
	const kecamatan = document.getElementById('kecamatan');
	const kelurahan = document.getElementById('kelurahan');

	const labelProvinsi = document.getElementById('labelProvinsi');
	const labelKota = document.getElementById('labelKota');
	const labelKecamatan = document.getElementById('labelKecamatan');
	const labelKelurahan = document.getElementById('labelKelurahan');

	const PLACEHOLDER_VALUE = '0';
	const csvBase = 'https://raw.githubusercontent.com/guzfirdaus/Wilayah-Administrasi-Indonesia/master/csv';
	const dataCache = {
		provinces: null,
		regencies: null,
		districts: null,
		villages: null,
	};

	function createPlaceholder(text) {
		return [{ id: PLACEHOLDER_VALUE, name: text }];
	}

	function setOptions(selectEl, items, placeholder) {
		selectEl.innerHTML = '';
		const allItems = createPlaceholder(placeholder).concat(items);

		allItems.forEach(function (item) {
			const option = document.createElement('option');
			option.value = item.id;
			option.textContent = item.name;
			selectEl.appendChild(option);
		});

		selectEl.value = PLACEHOLDER_VALUE;
		selectEl.disabled = items.length === 0;
	}

	function setLoading(selectEl, message) {
		selectEl.innerHTML = '<option value="' + PLACEHOLDER_VALUE + '">' + message + '</option>';
		selectEl.value = PLACEHOLDER_VALUE;
		selectEl.disabled = true;
	}

	function sanitizeValue(value) {
		return (value || '').replace(/\s+/g, ' ').trim();
	}

	function parseSemicolonCsv(text) {
		const rows = [];
		let row = [];
		let field = '';
		let inQuotes = false;

		for (let i = 0; i < text.length; i += 1) {
			const char = text[i];

			if (char === '"') {
				const nextChar = text[i + 1];
				if (inQuotes && nextChar === '"') {
					field += '"';
					i += 1;
				} else {
					inQuotes = !inQuotes;
				}
				continue;
			}

			if (char === ';' && !inQuotes) {
				row.push(sanitizeValue(field));
				field = '';
				continue;
			}

			if (char === '\n' && !inQuotes) {
				row.push(sanitizeValue(field));
				rows.push(row);
				row = [];
				field = '';
				continue;
			}

			if (char !== '\r') {
				field += char;
			}
		}

		if (field.length > 0 || row.length > 0) {
			row.push(sanitizeValue(field));
			rows.push(row);
		}

		const headers = rows[0] || [];
		return rows.slice(1)
			.filter(function (item) {
				return item.length >= headers.length && item[0] !== '';
			})
			.map(function (item) {
				const obj = {};
				headers.forEach(function (header, index) {
					obj[header] = sanitizeValue(item[index] || '');
				});
				return obj;
			});
	}

	async function fetchCsv(fileName) {
		const response = await axios.get(csvBase + '/' + fileName + '.csv', {
			responseType: 'text',
		});
		return parseSemicolonCsv(response.data);
	}

	async function getDataset(name) {
		if (dataCache[name]) {
			return dataCache[name];
		}

		dataCache[name] = fetchCsv(name);
		return dataCache[name];
	}

	function normalizeRow(item) {
		return {
			id: item.id,
			name: item.name,
		};
	}

	async function loadProvinces() {
		const data = await getDataset('provinces');
		return data.map(normalizeRow);
	}

	async function loadRegencies(provinceId) {
		const data = await getDataset('regencies');
		return data
			.filter(function (item) {
				return item.province_id === provinceId;
			})
			.map(normalizeRow);
	}

	async function loadDistricts(regencyId) {
		const data = await getDataset('districts');
		return data
			.filter(function (item) {
				return item.regency_id === regencyId;
			})
			.map(normalizeRow);
	}

	async function loadVillages(districtId) {
		const data = await getDataset('villages');
		return data
			.filter(function (item) {
				return item.district_id === districtId;
			})
			.map(normalizeRow);
	}

	function resetAfter(level) {
		if (level === 'provinsi') {
			setOptions(kota, [], 'Pilih Kota');
			setOptions(kecamatan, [], 'Pilih Kecamatan');
			setOptions(kelurahan, [], 'Pilih Kelurahan');
			labelKota.textContent = '-';
			labelKecamatan.textContent = '-';
			labelKelurahan.textContent = '-';
		}

		if (level === 'kota') {
			setOptions(kecamatan, [], 'Pilih Kecamatan');
			setOptions(kelurahan, [], 'Pilih Kelurahan');
			labelKecamatan.textContent = '-';
			labelKelurahan.textContent = '-';
		}

		if (level === 'kecamatan') {
			setOptions(kelurahan, [], 'Pilih Kelurahan');
			labelKelurahan.textContent = '-';
		}
	}

	function updateSummary(selectEl, labelEl) {
		const selectedText = selectEl.options[selectEl.selectedIndex]?.text || '-';
		labelEl.textContent = selectEl.value !== PLACEHOLDER_VALUE ? selectedText : '-';
	}

	async function loadProvinsi() {
		try {
			const data = await loadProvinces();
			setOptions(provinsi, data, 'Pilih Provinsi');
			provinsi.disabled = false;
		} catch (error) {
			setLoading(provinsi, 'Gagal memuat provinsi');
			console.error(error);
		}
	}

	provinsi.addEventListener('change', async function () {
		updateSummary(provinsi, labelProvinsi);
		resetAfter('provinsi');

		if (provinsi.value === PLACEHOLDER_VALUE) {
			return;
		}

		setLoading(kota, 'Memuat Kota...');

		try {
			const data = await loadRegencies(provinsi.value);
			setOptions(kota, data, 'Pilih Kota');
		} catch (error) {
			setLoading(kota, 'Gagal memuat kota');
			console.error(error);
		}
	});

	kota.addEventListener('change', async function () {
		updateSummary(kota, labelKota);
		resetAfter('kota');

		if (kota.value === PLACEHOLDER_VALUE) {
			return;
		}

		setLoading(kecamatan, 'Memuat kecamatan...');

		try {
			const data = await loadDistricts(kota.value);
			setOptions(kecamatan, data, 'Pilih kecamatan');
		} catch (error) {
			setLoading(kecamatan, 'Gagal memuat kecamatan');
			console.error(error);
		}
	});

	kecamatan.addEventListener('change', async function () {
		updateSummary(kecamatan, labelKecamatan);
		resetAfter('kecamatan');

		if (kecamatan.value === PLACEHOLDER_VALUE) {
			return;
		}

		setLoading(kelurahan, 'Memuat kelurahan...');

		try {
			const data = await loadVillages(kecamatan.value);
			setOptions(kelurahan, data, 'Pilih kelurahan');
		} catch (error) {
			setLoading(kelurahan, 'Gagal memuat kelurahan');
			console.error(error);
		}
	});

	kelurahan.addEventListener('change', function () {
		updateSummary(kelurahan, labelKelurahan);
	});

	loadProvinsi();
});
</script>
@endsection
