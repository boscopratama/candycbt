<?php if ($ac == '') : ?>
	<div class='row'>
		<div class='col-md-12'>
			<div class='box box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'><img src='../dist/img/svg/nilai.svg' width='30'> DATA UJIAN AKTIF</h3>
					<div class='box-tools pull-right btn-group'>
					</div>
				</div><!-- /.box-header -->
				<div class='box-body'><?= $info ?>
					<?php $jq = mysqli_query($koneksi, "SELECT * FROM nilai group by kode_ujian"); ?>
					<?php while ($jenis = mysqli_fetch_array($jq)) : ?>
						<div class='col-md-4'>
							<div class='box box-widget widget-user-2'>
								<div class='widget-user-header bg-blue'>
									<div class='widget-user-image'>
										<img class='img-thumbnail' src='../dist/img/svg/nilai.svg'>
									</div>
									<h3 class='widget-user-username'><?= $jenis['kode_ujian'] ?></h3>
									<?php $nama = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM jenis WHERE id_jenis='$jenis[kode_ujian]'")); ?>
									<h5 class='widget-user-desc'><?= $nama['nama'] ?></h5>
								</div>
								<div class='box-footer'>
									<a href="?pg=<?= $pg ?>&idu=<?= $jenis['kode_ujian'] ?>"> <button class='btn btn-flat btn-block bg-purple'>Lihat Nilai</button></a>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
		<?php if (isset($_GET['idu'])) : ?>
			<?php $idu = $_GET['idu']; ?>
			<input type='hidden' id='iduj' value='<?= $idu ?>'>
			<div class='col-md-12'>
				<div class='box box-solid'>
					<div class='box-header with-border'>
						<h3 class='box-title'><img src='../dist/img/svg/daftar_nilai.svg' width='30'> DAFTAR NILAI UJIAN <?= $idu ?></h3>
						<div class='box-tools pull-right btn-group'></div>
					</div><!-- /.box-header -->
					<div class='box-body'><?= $info ?>
						<div class='table-responsive'>
							<table class='table table-striped table-bordered'>
								<th>#</th>
								<th>Nama Mapel</th>
								<th>Kelas</th>
								<th>Action</th>
								<?php
										if ($pengawas['level'] == 'admin') {
											$mapelQ = mysqli_query($koneksi, "SELECT mapel.*, nilai.* FROM mapel JOIN nilai ON mapel.id_mapel=nilai.id_mapel WHERE nilai.kode_ujian='$idu' GROUP BY mapel.id_mapel ASC");
										} elseif ($pengawas['level'] == 'guru') {
											$mapelQ = mysqli_query($koneksi, "SELECT mapel.*,nilai.* FROM mapel INNER JOIN nilai ON mapel.id_mapel=nilai.id_mapel WHERE mapel.idguru='$pengawas[id_pengawas]' GROUP BY mapel.id_mapel ASC");
										}
										?>
								<?php while ($mapel = mysqli_fetch_array($mapelQ)) :  ?>
									<?php
												$cek = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_mapel='$mapel[id_mapel]' and ujian_selesai='' and id_siswa<>''"));
												$cek2 = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM jawaban WHERE id_mapel='$mapel[id_mapel]'"));
												if ($cek <> 0 or $cek2 == 0) :
													$dis = 'disabled';
												else :
													$dis = '';
												endif;
												$no++;
												?>
									<tr>
										<input type='hidden' id="txt<?= $mapel['id_mapel'] ?>" value="<?= $mapel['id_mapel'] ?>" />
										<td><?= $no ?></td>
										<td><small class='label bg-blue'><?= $mapel['nama'] ?></small> <small class='label bg-purple'><?= $mapel['level'] ?></small>
											<?php
														$dataArray = unserialize($mapel['kelas']);
														foreach ($dataArray as $key => $value) :
															echo "<small class='label label-success'>$value </small>&nbsp;";
														endforeach;
														?>
										</td>
										<td>
											<select id="me<?= $mapel['id_mapel'] ?>" class='idkel form-control select2' style='width:100%'>
												<option value=''></option>
												<?php
															$kelasQ = mysqli_query($koneksi, "SELECT * FROM kelas");
															while ($kelas = mysqli_fetch_array($kelasQ)) :
																echo "<option value='$kelas[id_kelas]'>$kelas[id_kelas]</option>";
															endwhile;
															?>
											</select>
										</td>
										<td>
											<a href='#' id='btnnilai' class='linknilai btn btn-sm btn-primary'><i class='fa fa-eye'></i> Lihat Hasil </a>
										</td>
									</tr>
								<?php endwhile; ?>
							</table>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		<?php endif; ?>
	</div>
<?php elseif ($ac == 'lihat') : ?>
	<?php
		$id_mapel = $_GET['idm'];
		$id_kelas = $_GET['idk'];
		$kode_ujian = $_GET['idu'];
		$mapel = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM mapel WHERE id_mapel='$id_mapel'"));
		?>
	<div class='row'>
		<div class='col-md-12'>
			<div class='box box-solid'>
				<div class='box-header with-border bg-blue'>
					<h3 class='box-title'><img src='../dist/img/svg/daftar_nilai.svg' width='30'> NILAI <?= $mapel['nama'] ?> <?= $id_kelas ?></h3>
					<div class='box-tools pull-right btn-group'>
						<button class='btn btn-sm btn-primary' onclick="frames['frameresult'].print()"><i class='fa fa-print'></i> Print</button>
						<a class='btn btn-sm btn-primary' href='report_excel.php?m=<?= $id_mapel ?>&k=<?= $id_kelas ?>&i=<?= $kode_ujian ?>'><i class='fa fa-file-excel-o'></i> Excel</a>
						<a class='btn btn-sm btn-primary' href='?pg=nilai'><i class='fa fa-times'></i> Keluar</a>
					</div>
				</div><!-- /.box-header -->
				<div class='box-body'>
					<div class='table-responsive'>
						<table class='table table-bordered table-striped'>
							<thead>
								<tr>
									<th width='5px'>#</th>
									<th>No Peserta</th>
									<th>Nama</th>
									<th>Kelas</th>
									<th>Lama Ujian</th>
									<th>Analisis</th>
									<th>Nilai PG</th>
									<th>Essai</th>
									<th>Total</th>
									<th>Jawaban</th>
								</tr>
							</thead>
							<tbody>
								<?php $siswaQ = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_kelas='$id_kelas'"); ?>
								<?php while ($siswa = mysqli_fetch_array($siswaQ)) : ?>
									<?php
											$no++;
											$ket = '';
											$esai = $lama = $jawaban = $skor = $total = '--';
											$kelas = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM kelas WHERE id_kelas='$id_kelas'"));
											$nilaiQ = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_mapel='$id_mapel' AND id_siswa='$siswa[id_siswa]' and kode_ujian='$kode_ujian'");
											$nilaiC = mysqli_num_rows($nilaiQ);
											$nilai = mysqli_fetch_array($nilaiQ);
											if ($nilaiC <> 0) :
												$lama = '';
												if ($nilai['ujian_mulai'] <> '' and $nilai['ujian_selesai'] <> '') :
													$selisih = strtotime($nilai['ujian_selesai']) - strtotime($nilai['ujian_mulai']);
													$jam = round((($selisih % 604800) % 86400) / 3600);
													$mnt = round((($selisih % 604800) % 3600) / 60);
													$dtk = round((($selisih % 604800) % 60));
													($jam <> 0) ? $lama .= "$jam jam " : null;
													($mnt <> 0) ? $lama .= "$mnt menit " : null;
													($dtk <> 0) ? $lama .= "$dtk detik " : null;
													$esai = "$nilai[nilai_esai]";
													$jawaban = "<small class='label bg-green'>$nilai[jml_benar] <i class='fa fa-check'></i></small>  <small class='label bg-red'>$nilai[jml_salah] <i class='fa fa-times'></i></small>";
													$skor = number_format($nilai['skor'], 2, '.', '');
													$total = "<small class='label bg-blue'>" . number_format($nilai['total'], 2, '.', '') . "</small>";
													$ket = "";
												elseif ($nilai['ujian_mulai'] <> '' and $nilai['ujian_selesai'] == '') :
													$selisih = strtotime($nilai['ujian_berlangsung']) - strtotime($nilai['ujian_mulai']);
													$jam = round((($selisih % 604800) % 86400) / 3600);
													$mnt = round((($selisih % 604800) % 3600) / 60);
													$dtk = round((($selisih % 604800) % 60));
													($jam <> 0) ? $lama .= "$jam jam " : null;
													($mnt <> 0) ? $lama .= "$mnt menit " : null;
													($dtk <> 0) ? $lama .= "$dtk detik " : null;
													$ket = "<i class='fa fa-spin fa-spinner' title='Sedang ujian'></i>";
													$skor = $total = '--';
												endif;
											endif;
											?>
									<tr>
										<td><?= $no ?></td>
										<td><?= $siswa['no_peserta'] ?></td>
										<td><?= $siswa['nama'] ?></td>
										<td><?= $kelas['nama'] ?></td>
										<td><?= $ket . " " . $lama ?></td>
										<td><?= $jawaban ?></td>
										<td><?= $skor ?></td>
										<td><?= $esai ?></td>
										<td><?= $total ?></td>
										<td>
											<?php if ($nilai['skor'] <> "") : ?>
												<?php
															$cekjawab = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM hasil_jawaban WHERE id_siswa='$siswa[id_siswa]' and id_mapel='$id_mapel'"));
															if ($cekjawab <> 0) :
																$ket = '';
																$link = "?pg=" . $pg . "&ac=esai&idu=" . $_GET['idu'] . "&idm=" . $id_mapel . "&idk=" . $id_kelas . "&ids=" . $siswa['id_siswa'];
																$link2 = "?pg=" . $pg . "&ac=jawaban&idu=" . $_GET['idu'] . "&idm=" . $id_mapel . "&idk=" . $id_kelas . "&ids=" . $siswa['id_siswa'];
															else :
																$ket = 'style="display:none"';
																$link = '#';
																$link2 = '#';
															endif;
															?>
												<a href='<?= $link ?>' class='btn btn-xs btn-success' <?= $ket ?>><i class='fa fa-pencil-square-o'></i>input esai</a>
												<a href='<?= $link2 ?>' class='btn btn-xs btn-success' <?= $ket ?>><i class='fa fa-search'></i> lihat</a>
											<?php endif; ?>
										</td>
									</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
						<iframe name='frameresult' src='report.php?m=<?= $id_mapel ?>&i=<?= $kode_ujian ?>&k=<?= $id_kelas ?>' style='border:none;width:1px;height:1px;'></iframe>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>
<?php elseif ($ac == 'esai') :
	$id_mapel = $_GET['idm'];
	$id_kelas = $_GET['idk'];
	$id_siswa = $_GET['ids'];
	$kode_ujian = $_GET['idu'];
	$nilai = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_mapel='$id_mapel' AND id_siswa='$id_siswa' AND kode_ujian='$kode_ujian'"));
	if (isset($_POST['simpanesai'])) :
		$jml_data = count($_POST['idsoal']);
		$id_soal = $_POST['idsoal'];
		$nilaiesai = $_POST['nilaiesai'];
		$nilai = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_mapel='$id_mapel' AND id_siswa='$id_siswa' AND kode_ujian='$kode_ujian'"));
		for ($i = 1; $i <= $jml_data; $i++) :
			$exec = mysqli_query($koneksi, "UPDATE hasil_jawaban SET nilai_esai='" . $nilaiesai[$i] . "' WHERE id_soal='" . $id_soal[$i] . "' AND jenis='2' and id_mapel='$id_mapel' AND id_ujian='$nilai[id_ujian]' AND id_siswa='$id_siswa'");
			(!$exec) ? $info = info("Gagal menyimpan!", "NO") : jump("?pg=nilai&ac=esai&idm=$id_mapel&idk=$id_kelas&ids=$id_siswa");
		endfor;
		$sqljumlah = mysqli_query($koneksi, "SELECT sum(nilai_esai) AS hasil FROM hasil_jawaban WHERE id_mapel='$id_mapel' AND id_siswa='$id_siswa' AND jenis='2'");
		$jumlah = mysqli_fetch_array($sqljumlah);
		$bobot = mysqli_fetch_array(mysqli_query($koneksi, "select * from mapel where id_mapel='$id_mapel'"));
		$nilai_esai1 = $jumlah['hasil'] * $bobot['bobot_esai'] / 100;
		$nilai_esai = number_format($nilai_esai1, 2, '.', '');
		$nilai_pg = number_format($nilai['skor'], 2, '.', '');
		$total = $nilai_esai + $nilai_pg;
		mysqli_query($koneksi, "UPDATE nilai SET nilai_esai='$nilai_esai',total='$total' WHERE id_mapel='$id_mapel' and id_siswa='$id_siswa' and id_ujian='$nilai[id_ujian]'");
	endif;
	$mapel = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM mapel WHERE id_mapel='$id_mapel'"));
	?>
	<div class='row'>
		<div class='col-md-12'>
			<form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
				<div class='box box-primary'>
					<div class='box-header with-border'>
						<h3 class='box-title'>Nilai Essai <?= $mapel['nama'] ?></h3>
						<div class='box-tools pull-right btn-group'>
							<!--<button class='btn btn-sm btn-default' onclick=frames['frameresult'].print()><i class='fa fa-print'></i> Print</button>-->
							<!--<a class='btn btn-sm btn-success' href='report_excel.php?m=$id_mapel&k=$id_kelas'><i class='fa fa-download'></i> Excel</a>-->
							<button class='btn btn-sm btn-primary' name='simpanesai'><i class='fa fa-check'></i> Simpan</button>
							<a class='btn btn-sm btn-danger' href='?pg=nilai&ac=lihat&idu=<?= $kode_ujian ?>&idm=<?= $id_mapel ?>&idk=<?= $id_kelas ?>'><i class='fa fa-times'></i></a>
						</div>
					</div><!-- /.box-header -->
					<div class='box-body'>
						<div class='table-responsive'>
							<table class='table table-bordered table-striped'>
								<thead>
									<tr>
										<th width='5px'>#</th>
										<th>Soal & Jawaban</th>
										<th width='100px'>Input Nilai</th>
									</tr>
								</thead>
								<tbody>
									<?php $jawabanQ = mysqli_query($koneksi, "SELECT * FROM hasil_jawaban WHERE id_mapel='$id_mapel' and id_siswa='$id_siswa' and jenis='2' and id_ujian='$nilai[id_ujian]' "); ?>
									<?php while ($jawaban = mysqli_fetch_array($jawabanQ)) : ?>
										<?php
												$no++;
												$soal = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM soal WHERE id_soal='$jawaban[id_soal]' and jenis='2' and id_mapel='$id_mapel' "));
												if ($soal['file'] == '') {
													$gambar = '';
												} else {
													$gambar = "<img src='$homeurl/$soal[file]' class='img-responsive' style='max-width:300px;'/><p>";
												}
												if ($soal['file1'] == '') {
													$gambar2 = '';
												} else {
													$gambar2 = "<img src='$homeurl/$soal[file1]' class='img-responsive' style='max-width:300px;'/><p>";
												}
												?>
										<tr><input type='hidden' value='<?= $jawaban['id_soal'] ?>' name='idsoal[<?= $no ?>]'>
											<td><?= $no ?></td>
											<td><?= $gambar ?> <?= $gambar2 ?> <?= $soal['soal'] ?><p><b>Jawaban :</b> <?= $jawaban['esai'] ?></td>
											<td><input type='text' class='form-control' value="<?= $jawaban['nilai_esai'] ?>" name='nilaiesai[<?= $no ?>]'></td>
										</tr>
									<?php endwhile; ?>
								</tbody>
							</table>
							<iframe name='frameresult' src='report.php?m=<?= $id_mapel ?>&k=<?= $id_kelas ?>' style='border:none;width:1px;height:1px;'></iframe>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</form>
		</div>
	</div>
<?php elseif ($ac == 'jawaban') : ?>
	<?php
		$idmapel = $_GET['idm'];
		$kode_ujian = $_GET['idu'];
		$id_kelas = $_GET['idk'];
		$id_siswa = $_GET['ids'];
		$nilai = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa='$id_siswa' and id_mapel='$idmapel' and kode_ujian='$kode_ujian'"));
		$mapel = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM mapel where id_mapel='$nilai[id_mapel]'"));
		$namamapel = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM mata_pelajaran WHERE kode_mapel='$mapel[nama]'"));
		$siswa = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa='$id_siswa'"));
		?>
	<div class='row'>
		<div class='col-md-12'>
			<div class='box box-solid'>
				<div class='box-header with-border bg-blue'>
					<h3 class='box-title'>Data Hasil Ujian</h3>
					<div class='box-tools pull-right btn-group'>
						<button class='btn btn-sm btn-primary' onclick="frames['framejawab'].print()"><i class='fa fa-print'></i> Print</button>
						<a class='btn btn-sm btn-danger' href='?pg=nilai&ac=lihat&idu=<?= $kode_ujian ?>&idm=<?= $idmapel ?>&idk=<?= $id_kelas ?>'><i class='fa fa-times'></i></a>
						<iframe name='framejawab' src='printjawab.php?m=<?= $idmapel ?>&k=<?= $id_kelas ?>&s=<?= $id_siswa ?>' style='display:none;'></iframe>
					</div>
				</div><!-- /.box-header -->
				<div class='box-body'>
					<table class='table table-bordered table-striped'>
						<tr>
							<th width='150'>No Induk</th>
							<td width='10'>:</td>
							<td><?= $siswa['nis'] ?></td>
							<td style="text-align:center; width:150">Total Nilai</td>
						</tr>
						<tr>
							<th>Nama</th>
							<td width='10'>:</td>
							<td><?= $siswa['nama'] ?></td>
							<td rowspan='3' style='font-size:30px; text-align:center; width:150'><?= $nilai['total'] ?></td>
						</tr>
						<tr>
							<th>Kelas</th>
							<td width='10'>:</td>
							<td><?= $siswa['id_kelas'] ?></td>
						</tr>
						<tr>
							<th>Mata Pelajaran</th>
							<td width='10'>:</td>
							<td><?= $namamapel['nama_mapel'] ?></td>
						</tr>
					</table>
					<br>
					<table class='table table-bordered table-striped'>
						<thead>
							<tr>
								<th width='5px'>#</th>
								<th>Soal PG</th>
								<th style='text-align:center'>Jawab</th>
								<th style='text-align:center'>Hasil</th>
							</tr>
						</thead>
						<tbody>
							<?php $nilaix = mysqli_query($koneksi, "SELECT * FROM hasil_jawaban WHERE id_siswa='$id_siswa' and id_mapel='$idmapel' and id_ujian='$nilai[id_ujian]' and jenis='1' "); ?>
							<?php while ($jawaban = mysqli_fetch_array($nilaix)) : ?>
								<?php
										$no++;
										$soal = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM soal WHERE id_soal='$jawaban[id_soal]'"));
										if ($jawaban['jawaban'] == $soal['jawaban']) :
											$status = "<span class='text-green'><i class='fa fa-check'></i></span>";
										else :
											$status = "<span class='text-red'><i class='fa fa-times'></i></span>";
										endif;
										?>
								<tr>
									<td><?= $no ?></td>
									<td><?= $soal['soal'] ?></td>
									<td style='text-align:center'><?= $jawaban['jawaban'] ?></td>
									<td style='text-align:center'><?= $status ?></td>
								</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
					<br>
					<table class='table table-bordered table-striped'>
						<thead>
							<tr>
								<th width='5px'>#</th>
								<th>Soal Esai</th>
								<th style='text-align:center'>Hasil</th>
							</tr>
						</thead>
						<tbody>
							<?php $nilaiex = mysqli_query($koneksi, "SELECT * FROM hasil_jawaban WHERE id_siswa='$id_siswa' and id_mapel='$idmapel' and jenis='2' and id_ujian='$nilai[id_ujian]' "); ?>
							<?php while ($jawabane = mysqli_fetch_array($nilaiex)) : ?>
								<?php
										$soal = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM soal WHERE id_soal='$jawabane[id_soal]'"));
										$nox++;
										?>
								<tr>
									<td><?= $nox ?></td>
									<td><?= $soal['soal'] ?>
										<p><b>jawab : </b><?= $jawabane['esai'] ?></p>
									</td>
									<td style='text-align:center'><?= $jawabane['nilai_esai'] ?></td>
								</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endif ?>