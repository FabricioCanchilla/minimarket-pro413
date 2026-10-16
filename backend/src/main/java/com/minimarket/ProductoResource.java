package com.minimarket;

import java.net.URI;
import java.util.List;
import java.util.Map;

import jakarta.transaction.Transactional;
import jakarta.validation.Valid;
import jakarta.ws.rs.Consumes;
import jakarta.ws.rs.DELETE;
import jakarta.ws.rs.GET;
import jakarta.ws.rs.NotFoundException;
import jakarta.ws.rs.POST;
import jakarta.ws.rs.PUT;
import jakarta.ws.rs.Path;
import jakarta.ws.rs.PathParam;
import jakarta.ws.rs.Produces;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;

@Path("/productos")
@Produces(MediaType.APPLICATION_JSON)
@Consumes(MediaType.APPLICATION_JSON)
public class ProductoResource {

    // Consultar: solo productos activos (eliminado = false)
    @GET
    public List<Producto> listar() {
        return Producto.list("eliminado = ?1 order by id", false);
    }

    // Consultar uno (lo usará el formulario de edición del frontend)
    @GET
    @Path("/{id}")
    public Producto obtener(@PathParam("id") Integer id) {
        Producto p = Producto.findById(id);
        if (p == null || p.eliminado) {
            throw noEncontrado(id);
        }
        return p;
    }

    // Registrar
    @POST
    @Transactional
    public Response crear(@Valid Producto datos) {
        datos.id = null;
        datos.eliminado = false;
        datos.persist();
        return Response.created(URI.create("/productos/" + datos.id))
                .entity(datos)
                .build();
    }

    // Editar
    @PUT
    @Path("/{id}")
    @Transactional
    public Producto editar(@PathParam("id") Integer id, @Valid Producto datos) {
        Producto p = Producto.findById(id);
        if (p == null || p.eliminado) {
            throw noEncontrado(id);
        }
        p.nombre = datos.nombre;
        p.proveedor = datos.proveedor;
        p.fecha_vencimiento = datos.fecha_vencimiento;
        p.precio = datos.precio;
        p.stock = datos.stock;
        return p;
    }

    // Eliminar lógicamente: solo cambia eliminado a true, no borra la fila
    @DELETE
    @Path("/{id}")
    @Transactional
    public Response eliminar(@PathParam("id") Integer id) {
        Producto p = Producto.findById(id);
        if (p == null || p.eliminado) {
            throw noEncontrado(id);
        }
        p.eliminado = true;
        return Response.noContent().build();
    }

    private NotFoundException noEncontrado(Integer id) {
        return new NotFoundException(Response.status(Response.Status.NOT_FOUND)
                .entity(Map.of("error", "Producto con id " + id + " no encontrado"))
                .build());
    }
}
