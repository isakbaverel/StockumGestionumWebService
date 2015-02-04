<div id="maj_panier_head" style="display:none;"><?php echo ROOT_URL.'?module=user&action=maj_panier_head'; ?></div>
<div id="maj_panier_side" style="display:none;"><?php echo ROOT_URL.'?module=user&action=maj_panier_side'; ?></div>
<input type="hidden" id="quantite" value="1" />
		<div class="span9">		
			<div class="well ">
			 <h3>Nouveautés </h3>
		<div id="message"></div>
		<ul class="thumbnails">
				<?php
				foreach($livres_new as $livre){
					if(in_array($livre['ID_Exemplaire'], $ids_dedou_new)){
						$key = array_search($livre['ID_Exemplaire'], $ids_dedou_new);
						unset($ids_dedou_new[$key]);
					?>
				<li class="span3.5">
				  <div class="thumbnail">
				  	<?php
						$date=strtotime($livre['dt'])+(10*24*3600);

						//if((strtotime( date('Y-M-d')) <= $date)){
							echo '<i class="tag"></i>';
						//}
					?>
					<a href="<?php echo ROOT_URL.'?module=book&action=display&id='.$livre['ID_Exemplaire']; ?>"><img src="<?php echo './View/Images/'.$livre['chemin']; ?>" style="min-height:150px;height:150px;" alt=""/>
					<div class="caption">
						<?php
							$lg_max = 28; //nombre de caractère autoriser
							$chaine =$livre['tite']; 
							if (strlen($chaine) > $lg_max)
							{
								$chaine = substr($chaine, 0, $lg_max);
								$last_space = strrpos($chaine, " ");
								$chaine = substr($chaine, 0, $last_space)." ...";
							}
						?>
					  <h5><?php echo $chaine; ?></h5>
					  <p> 
						Auteur : <?php echo $livre['prenom'].' '.$livre['nom'];?>
					  </p></a>
					   <h4 style="text-align:center">
					   <a class="btn" href="<?php echo ROOT_URL.'?module=book&action=display&id='.$livre['ID_Exemplaire']; ?>"> <i class="icon-zoom-in"></i></a>
					   <?php
					   		if($livre['ID_Createur'] == null){
					   	?>
							   <a class="btn add" href="<?php echo ROOT_URL.'?module=user&action=add_panier&id='.$id.'&idex='.$livre['ID_Exemplaire'].'&quantite=' ; ?>">Ajouter <i class="icon-shopping-cart"></i></a> 
							   <a class="btn btn-primary"><?php echo getPrixTTC($livre['prix'],$livre['TVA']); ?> &euro;</a>
						<?php
							}else{	
						?>
							<a class="btn btn-primary" href="<?php echo ROOT_URL.'?module=book&action=display&id='.$livre['ID_Exemplaire']; ?>">Projet de livre</a>
						<?php
							}
					   ?>
					   </h4>
				  </div>
				</li>
				<?php
				}
			}
				?>
			  </ul>
			</div>

			<div class="well ">

		<h3>Meilleures ventes  </h3>
		<div id="message"></div>
		<ul class="thumbnails">
				<?php
				foreach($livres as $livre){
					if(in_array($livre['ID_Exemplaire'], $ids_dedou)){
						$key = array_search($livre['ID_Exemplaire'], $ids_dedou);
						unset($ids_dedou[$key]);
					?>
				<li class="span3.5">
				  <div class="thumbnail">
				  	<?php
						$date=strtotime($livre['dt'])+(10*24*3600);

						if((strtotime( date('Y-M-d')) <= $date)){
							echo '<i class="tag"></i>';
						}
					?>
					<a href="<?php echo ROOT_URL.'?module=book&action=display&id='.$livre['ID_Exemplaire']; ?>"><img src="<?php echo './View/Images/'.$livre['chemin']; ?>" style="min-height:150px;height:150px;" alt=""/>
					<div class="caption">
						<?php
							$lg_max = 28; //nombre de caractère autoriser
							$chaine =$livre['tite']; 
							if (strlen($chaine) > $lg_max)
							{
								$chaine = substr($chaine, 0, $lg_max);
								$last_space = strrpos($chaine, " ");
								$chaine = substr($chaine, 0, $last_space)." ...";
							}
						?>
					  <h5><?php echo $chaine; ?></h5>
					  <p> 
						Auteur : <?php echo $livre['prenom'].' '.$livre['nom'];?>
					  </p></a>
					   <h4 style="text-align:center">
					   	<a class="btn" href="<?php echo ROOT_URL.'?module=book&action=display&id='.$livre['ID_Exemplaire']; ?>"> <i class="icon-zoom-in"></i></a>
					   	<?php
					   		if($livre['ID_Createur'] == null){
					   	?>
							   <a class="btn add" href="<?php echo ROOT_URL.'?module=user&action=add_panier&id='.$id.'&idex='.$livre['ID_Exemplaire'].'&quantite=' ; ?>">Ajouter <i class="icon-shopping-cart"></i></a> 
							   <a class="btn btn-primary"><?php echo getPrixTTC($livre['prix'],$livre['TVA']); ?> &euro;</a>
						<?php
							}else{	
						?>
							<a class="btn btn-primary" href="<?php echo ROOT_URL.'?module=book&action=display&id='.$livre['ID_Exemplaire']; ?>">Projet de livre</a>
						<?php
							}
					   ?>
					   </h4>
				  </div>
				</li>
				<?php
				}
			}
				?>
			  </ul>
			</div>
			 
		</div>



		</div>
	</div>
</div>