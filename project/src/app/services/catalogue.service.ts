import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Produit {
  id_produit: number;
  nom: string;
  description: string;
  montant: number;
  photo: string;
}

export interface Categorie {
  id_categorie: number;
  nom: string;
}

@Injectable({
  providedIn: 'root'
})
export class CatalogueService {
  constructor(private http: HttpClient) {}

  getCatalogue(params: any): Observable<any> {
    return this.http.get('/api/catalogue', { params });
  }
}
