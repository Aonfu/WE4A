import { Routes } from '@angular/router';
import { CatalogueComponent } from './catalogue/catalogue.component';
import { ConnexionComponent } from './connexion/connexion.component';
import { InscriptionComponent } from './inscription/inscription.component';
import { EnchereComponent } from './enchere/enchere.component';
import { VenteComponent } from './vente/vente.component';
import { MonEspaceComponent } from './mon_espace/mon_espace.component';
import { EditerProduitComponent } from './editer_produit/editer_produit.component';


export const routes: Routes = [
  { path: '', component: CatalogueComponent },
  { path: 'catalogue', component: CatalogueComponent },
  { path: 'connexion', component: ConnexionComponent },
  { path: 'inscription', component: InscriptionComponent },
  { path: 'enchere/:id', component: EnchereComponent },
  { path: 'vente', component: VenteComponent },
  { path: 'mon_espace', component: MonEspaceComponent },
  { path: 'editer_produit/:id', component: EditerProduitComponent },
  { path: 'supprimer_produit/:id', component: EditerProduitComponent }
];