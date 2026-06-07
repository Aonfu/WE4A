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

  constructor(private http: HttpClient, private router: Router, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    this.http.get<any>('/api/vente.php').subscribe((data: any) => {
      this.categories = data.categories;
      this.cdr.detectChanges();
    });
  }

  onPhotoChange(event: any) {
    this.photoFile = event.target.files[0];
  }

  soumettre(nom: string, categorie: string, description: string, prix: string, date_fin: string) {
    const formData = new FormData();
    formData.append('nom', nom);
    formData.append('categorie', categorie);
    formData.append('description', description);
    formData.append('prix', prix);
    formData.append('date_fin', date_fin);
    formData.append('photo', this.photoFile!);
    this.http.post<any>('/api/vente.php', formData).subscribe((data: any) => {
          if (data.success) {
        this.router.navigate(['/mon_espace']);
      }
    });
  }
}