import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-vente',
  templateUrl: './vente.component.html'
})
export class VenteComponent implements OnInit {
  categories: any[] = [];
  minDate = new Date().toISOString().slice(0, 16);
  photoFile: File | null = null;
  userId: string = '';

  constructor(private http: HttpClient, private router: Router, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    // Récupère l'utilisateur connecté
    const user = localStorage.getItem('user');
    if (user) {
      const userData = JSON.parse(user);
      // Utilise l'id ou l'email si l'id n'existe pas
      this.userId = userData.id || userData.email || 'unknown';
    }

    this.http.get<any>('/api/vente.php').subscribe((data: any) => {
      this.categories = data.categories;
      this.cdr.detectChanges();
    });
  }

  onPhotoChange(event: any) {
    this.photoFile = event.target.files[0];
  }

  soumettre(nom: string, categorie: string, description: string, prix: string, date_fin: string) {
    // Vérifier que userId n'est pas 'unknown'
    if (this.userId === 'unknown') {
      console.error('Utilisateur non connecté');
      return;
    }

    const formData = new FormData();
    formData.append('nom', nom);
    formData.append('categorie', categorie);
    formData.append('description', description);
    formData.append('prix', prix);
    formData.append('date_fin', date_fin);
    formData.append('photo', this.photoFile!);
    formData.append('id_utilisateur', this.userId);

    // Upload l'image sur mongo DB
    const imageFormData = new FormData();
    imageFormData.append('image', this.photoFile!);
    imageFormData.append('userId', this.userId);

    this.http.post<any>('http://localhost:3000/api/files/upload', imageFormData).subscribe({
      next: (imageData) => {
        console.log('📸 Image uploadée:', imageData);

        // Envoi du formulaire à PHP
        this.http.post<any>('/api/vente.php', formData).subscribe({
          next: (data: any) => {
            if (data.success) {
              // Log de la création du produit
              this.http.post('http://localhost:3000/api/logs/create', {
                userId: this.userId,
                action: 'create_product'
              }).subscribe();

              // Incrémenter la stat product
              this.http.post('http://localhost:3000/api/stats/create', {
                metric: 'product'
              }).subscribe();

              this.router.navigate(['/mon_espace']);
            } else {
              console.error('Erreur PHP:', data.error);
            }
          },
          error: (err) => {
            console.error('Erreur envoi formulaire:', err);
          }
        });
      },
      error: (err) => {
        console.error('Erreur upload image:', err);
      }
    });
  }
}
