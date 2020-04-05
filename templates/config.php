<?php
try {
	if (isset($_GET['command'])) {
		$checking = false;

		switch ($_GET['command']) {
			case 'init':
				$output = $this->model->_Composer->initCommand();
				break;
			case 'update':
				$output = $this->model->_Composer->updateCommand();
				break;
			case 'install':
				$output = $this->model->_Composer->installCommand();
				break;
			default:
				throw new \Exception('Unrecognized command');
				break;
		}

		if ($output) {
			?>
			<div class="green-message"><?= entities($output, true) ?></div>
			<?php
		}
	} else {
		$checking = true;
		$this->model->_Composer->check();
	}
} catch (Exception $e) {
	?>
	<div class="red-message"><?= entities($e->getMessage(), true) ?></div>
	<?php
	if ($checking)
		return;
}


$composerFile = $this->model->_Composer->getJson();
if (!$composerFile) {
	?>
	<div class="orange-message">composer.json was not found or it is invalid; run init command</div>
	<?php
}
?>
<style>
	td {
		padding: 5px;
	}
</style>

<h2>Composer</h2>

<p>
	<b>Comandi disponibili:</b>
	[<a href="?command=init"> init </a>]
	[<a href="?command=install"> install </a>]
	[<a href="?command=update"> update </a>]
</p>

<?php
if (!$composerFile)
	return;
?>

<hr/>

<form action="?" method="post">
	<table id="cont-packages">
		<tr style="color: #2693FF">
			<td>
				Delete?
			</td>
			<td>
				Package
			</td>
			<td>
				Version
			</td>
		</tr>
		<?php
		$packages = $composerFile['require'] ?? [];
		foreach ($packages as $packageName => $packageVersion) {
			?>
			<tr>
				<td>
					<input type="checkbox" name="delete-<?= entities($packageName) ?>" value="yes"/>
				</td>
				<td>
					<input type="text" name="<?= entities($packageName) ?>-package" value="<?= entities($packageName) ?>"/>
				</td>
				<td>
					<input type="text" name="<?= entities($packageName) ?>-version" value="<?= entities($packageVersion) ?>"/>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td>
				New:
			</td>
			<td>
				<input type="text" name="new-package"/>
			</td>
			<td>
				<input type="text" name="new-version" value="*"/>
			</td>
		</tr>
	</table>

	<p>
		<input type="submit" value="Save"/>
	</p>
</form>