import { Routes } from '@angular/router';
import { CatalogueComponent } from './pages/catalogue/catalogue';

export const routes: Routes = [
  { path: '', component: CatalogueComponent },
  { path: 'catalogue', component: CatalogueComponent },
];
