import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-mon-espace',
  templateUrl: './mon_espace.component.html'
})
export class MonEspaceComponent implements OnInit {
  enchere_gagne = 0;
  montant_depense = 0;
  winrate = 0;
  enchere_en_cours = 0;
  categorie_favorite = 'Aucune';
  nb_vente = 0;
  revenu_total = 0;
  plus_grosse_vente = 0;
  encheres_gagnees: any[] = [];
  encheres_en_cours: any[] = [];
  produits_en_vente: any[] = [];

  constructor(private http: HttpClient, private router: Router, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    this.http.get<any>('/api/mon_espace.php').subscribe((data: any) => {
      if (data.error === 'non_connecte') {
        this.router.navigate(['/connexion']);
        return;
      }
      this.enchere_gagne = data.enchere_gagne;
      this.montant_depense = data.montant_depense;
      this.winrate = data.winrate;
      this.enchere_en_cours = data.enchere_en_cours;
      this.categorie_favorite = data.categorie_favorite;
      this.nb_vente = data.nb_vente;
      this.revenu_total = data.revenu_total;
      this.plus_grosse_vente = data.plus_grosse_vente;
      this.encheres_gagnees = data.encheres_gagnees;
      this.encheres_en_cours = data.encheres_en_cours;
      this.produits_en_vente = data.produits_en_vente;
      this.cdr.detectChanges();
    });
  }
}