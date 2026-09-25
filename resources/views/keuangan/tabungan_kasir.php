<div class="z-content-pad">


        
        <div class="z-panel-body" style="padding: 24px; border-bottom: 1px solid var(--z-border);">
            <div style="position: relative;">
                <input type="text" id="searchInput" class="z-input" placeholder="Ketik nama atau NIS siswa..." style="padding: 15px 20px; font-size: 1rem; border-radius: 12px; height: auto;" autocomplete="off">
                <div class="search-results" id="searchResults"></div>
            </div>
            <div style="font-size: 0.8rem; color: var(--z-muted); margin-top: 10px;">
                <i data-lucide="info" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> 
                Ketik minimal 2 karakter untuk memulai pencarian.
            </div>
        </div>
        
        <!-- Hasil Terpilih (Awalnya Sembunyi) -->
        <div id="selectedStudent" class="z-panel-body" style="text-align: center; display: none; padding: 40px 24px;">
            <div style="width: 100px; height: 100px; margin: 0 auto 16px;">
                <img id="selFoto" src="" alt="Foto Siswa" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 4px solid #eff6ff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            </div>
            <div id="selNama" style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">-</div>
            <div style="display: flex; justify-content: center; gap: 10px; margin-top: 8px;">
                <span style="font-family: monospace; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.9rem;" id="selNis">-</span>
                <span style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; font-size: 0.9rem; font-weight: 700; color: #64748b;" id="selKelas">-</span>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px;">
                <div style="font-size: 0.85rem; color: #64748b; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; margin-bottom: 5px;">Saldo Tabungan</div>
                <div id="selSaldo" style="font-size: 2.2rem; font-weight: 900; color: #10b981;">Rp 0</div>
            </div>
        </div>
        
        <!-- Welcome State -->
        <div id="welcomeState" class="z-panel-body" style="text-align: center; padding: 60px 24px;">
            <div style="width: 80px; height: 80px; background: var(--z-bg); color: var(--z-muted); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i data-lucide="users" style="width: 40px; height: 40px;"></i>
            </div>
            <div style="font-weight: 700; color: var(--z-muted);">Silakan cari dan pilih siswa terlebih dahulu.</div>
        </div>
    </div>

    <!-- Panel Transaksi -->
    <!-- Panel Transaksi -->
    <div class="z-panel" style="opacity: 0.5; pointer-events: none; transition: all 0.3s ease;" id="transactionPanel">
        <div class="z-panel-head">
            <div class="z-panel-title"><i data-lucide="credit-card" style="color: #f59e0b;"></i> Form Transaksi</div>
        </div>
        
        <div class="z-panel-body">
            <form method="POST" action="/admin/tabungan/transaksi">
                <input type="hidden" name="siswa_id" id="formSiswaId">
                <input type="hidden" name="kelas_id" value="0">
                <input type="hidden" name="redirect" value="/admin/tabungan/kasir">
                
                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="jenis_mutasi" value="Setor" checked style="display: none;" id="radioSetor" onchange="updateFormUI()">
                        <div class="radio-card" id="cardSetor" style="padding: 12px; text-align: center; border: 2px solid #10b981; border-radius: 10px; background: #ecfdf5; color: #10b981; font-weight: 700; transition: all 0.2s;">
                            <i data-lucide="arrow-down-to-line" style="margin-bottom: 5px;"></i><br>Setor Tunai
                        </div>
                    </label>
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="jenis_mutasi" value="Tarik" style="display: none;" id="radioTarik" onchange="updateFormUI()">
                        <div class="radio-card" id="cardTarik" style="padding: 12px; text-align: center; border: 2px solid var(--z-border); border-radius: 10px; background: white; color: var(--z-muted); font-weight: 700; transition: all 0.2s;">
                            <i data-lucide="arrow-up-from-line" style="margin-bottom: 5px;"></i><br>Tarik Tunai
                        </div>
                    </label>
                </div>

                <div class="z-form-group">
                    <label class="z-label" style="font-size: 0.85rem;">Nominal (Rp)</label>
                    <input type="number" name="jumlah" id="formJumlah" class="z-input" required min="1000" step="100" placeholder="0" style="font-size: 1.5rem; font-weight: 800; padding: 15px; height: auto;">
                </div>

                <div class="z-form-group">
                    <label class="z-label" style="font-size: 0.85rem;">Keterangan / Catatan</label>
                    <input type="text" name="keterangan" class="z-input" placeholder="Opsional...">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 1.1rem; font-weight: 800; justify-content: center; box-shadow: 0 4px 15px rgba(16,185,129,0.3);" id="btnSubmit">
                    Simpan Setoran
                </button>
            </form>
        </div>
    </div>

</div>

</div> <!-- /z-scroll -->

</div>