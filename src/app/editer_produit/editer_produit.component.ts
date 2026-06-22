import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-editer-produit',
  templateUrl: './editer_produit.component.html'
})
export class EditerProduitComponent implements OnInit {
  produit: any = null;
  categories: any[] = [];
  minDate = '';
  photoFile: File | null = null;
  id: any = null;
  userId: string = '';

  constructor(private http: HttpClient, private route: ActivatedRoute, private router: Router, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    const user = localStorage.getItem('user');
    if (user) {
      const userData = JSON.parse(user);
      this.userId = userData.email || userData.id || 'unknown';
    }

    this.id = this.route.snapshot.paramMap.get('id');
    this.http.get<any>(`/api/editer_produit.php?id=${this.id}`).subscribe((data: any) => {
      this.produit = data.produit;
      this.categories = data.categories;
      this.minDate = data.min_date;
      this.cdr.detectChanges();
    });
  }

  onPhotoChange(event: any) {
    this.photoFile = event.target.files[0];
  }

  modifier(nom: string, categorie: string, description: string) {
    const formData = new FormData();
    formData.append('nom', nom);
    formData.append('categorie', categorie);
    formData.append('description', description);
    if (this.photoFile) {
      formData.append('photo', this.photoFile);

      // Si nouvelle photo, upload vers Node.js
      const imageFormData = new FormData();
      imageFormData.append('image', this.photoFile);
      imageFormData.append('userId', this.userId);
      this.http.post<any>('http://localhost:3000/api/files/upload', imageFormData).subscribe();
    }

    // Mise à jour du produit dans la BDD
    this.http.post<any>(`/api/editer_produit.php?id=${this.id}`, formData).subscribe((data: any) => {
      if (data.success) {
        // Log pour édition de produit
        this.http.post('http://localhost:3000/api/logs/create', {
          userId: this.userId,
          action: 'edit_product',
          details: { productId: this.id }
        }).subscribe();

        this.router.navigate(['/mon_espace']);
      }
    });
  }

  supprimer() {
    if (confirm('Voulez vous vraiment supprimer ce produit ?')) {
      this.http.post<any>('/api/supprimer_produit.php', { id: this.id }).subscribe((data: any) => {
        if (data.success) {
          // Log pour suppression de produit
          this.http.post('http://localhost:3000/api/logs/create', {
            userId: this.userId,
            action: 'delete_product',
            details: { productId: this.id }
          }).subscribe();

          this.router.navigate(['/mon_espace']);
        }
      });
    }
  }
}
