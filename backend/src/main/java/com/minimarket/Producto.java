package com.minimarket;
import java.math.BigDecimal;
import java.time.LocalDate;
import io.quarkus.hibernate.orm.panache.PanacheEntityBase;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import jakarta.validation.constraints.DecimalMin;
import jakarta.validation.constraints.Digits;
import jakarta.validation.constraints.Min;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Size;

@Entity
@Table(name = "Producto")
public class Producto extends PanacheEntityBase {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    public Integer id;

    @NotBlank(message = "El nombre es obligatorio")
    @Size(max = 100, message = "El nombre admite máximo 100 caracteres")
    public String nombre;

    @NotBlank(message = "El proveedor es obligatorio")
    @Size(max = 100, message = "El proveedor admite máximo 100 caracteres")
    public String proveedor;

    @NotNull(message = "La fecha de vencimiento es obligatoria")
    public LocalDate fecha_vencimiento;

    @NotNull(message = "El precio es obligatorio")
    @DecimalMin(value = "0.0", message = "El precio no puede ser negativo")
    @Digits(integer = 8, fraction = 2, message = "Precio inválido (máx. 8 enteros y 2 decimales)")
    public BigDecimal precio;

    @NotNull(message = "El stock es obligatorio")
    @Min(value = 0, message = "El stock no puede ser negativo")
    public Integer stock;

    public boolean eliminado = false;
}