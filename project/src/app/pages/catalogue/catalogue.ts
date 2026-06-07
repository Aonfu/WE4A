import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { CatalogueService, Produit, Categorie } from '../../services/catalogue.service';

@Component({
  selector: 'app-catalogue',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './catalogue.html',
  styleUrls: ['./catalogue.css']
})
export class CatalogueComponent implements OnInit {
  produits: Produit[] = [];
  categories: Categorie[] = [];
  loading = true;

  // Filtres
  searchTerm: string = '';
  selectedCategorie: number | null = null;
  prixMin: number | null = null;
  prixMax: number | null = null;
  tri: string = '';

  constructor(private catalogueService: CatalogueService) {}

  ngOnInit() {
    this.loadCatalogue();
  }

  loadCatalogue() {
    this.loading = true;
    const params: any = {};

    if (this.searchTerm) params.search = this.searchTerm;
    if (this.selectedCategorie) params.categorie = this.selectedCategorie;
    if (this.prixMin) params.prix_min = this.prixMin;
    if (this.prixMax) params.prix_max = this.prixMax;
    if (this.tri) params.tri = this.tri;

    this.catalogueService.getCatalogue(params).subscribe({
      next: (data) => {
        this.produits = data.produits;
        this.categories = data.categories;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur:', err);
        this.loading = false;
      }
    });
  }

  appliquerFiltres() {
    this.loadCatalogue();
  }

  resetFiltres() {
    this.searchTerm = '';
    this.selectedCategorie = null;
    this.prixMin = null;
    this.prixMax = null;
    this.tri = '';
    this.loadCatalogue();
  }
}
