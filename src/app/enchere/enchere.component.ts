import { Component, OnInit, OnDestroy, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-enchere',
  templateUrl: './enchere.component.html'
})
export class EnchereComponent implements OnInit, OnDestroy {
  produit: any = null;
  enchere_max = 0;
  enchere_min = 0;
  gagnant: any = null;
  historique: any[] = [];
  termine = false;
  id_utilisateur_session: any = null;
  role: any = null;
  id: any = null;
  private interval: any;

  constructor(private http: HttpClient, private route: ActivatedRoute, private router: Router, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    this.id = this.route.snapshot.paramMap.get('id');
    this.charger();
    // Mise à jour toutes les 3 secondes par une requete AJAX
    this.interval = setInterval(() => {
      this.http.get<any>(`/api/get_data_enchere.php?id=${this.id}`).subscribe((data: any) => {
        this.enchere_min = data.enchere_min;
        this.produit.date_fin = data.date_fin;
        this.enchere_max = data.prix_actuel;
        this.historique = data.historique;
        this.termine = data.termine;
        this.gagnant = data.gagnant;
        this.cdr.detectChanges();
      });
    }, 3000);
  }

  ngOnDestroy() {
    clearInterval(this.interval);
  }

  charger() {
    this.http.get<any>(`/api/enchere.php?id=${this.id}`).subscribe((data: any) => {
      if (data.error === 'non_connecte') {
        this.router.navigate(['/connexion']);
        return;
      }
      this.produit = data.produit;
      this.enchere_max = data.enchere_max;
      this.enchere_min = data.enchere_min;
      this.gagnant = data.gagnant;
      this.historique = data.historique;
      this.termine = data.termine;
      this.id_utilisateur_session = data.id_utilisateur_session;
      this.role = data.role;
      this.cdr.detectChanges();
    });
  }

  placerEnchere(montant: string) {
    this.http.post<any>(`/api/enchere.php?id=${this.id}`, { montant: +montant }).subscribe(() => {
      this.charger();
    });
  }

  supprimerProduit() {
    if (confirm('Voulez-vous vraiment supprimer ce produit ?')) {
      this.router.navigate(['/supprimer_produit', this.id]);
    }
  }
}