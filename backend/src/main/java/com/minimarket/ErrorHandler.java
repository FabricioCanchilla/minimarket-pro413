package com.minimarket;

import java.util.Map;

import org.hibernate.HibernateException;
import org.jboss.logging.Logger;
import org.jboss.resteasy.reactive.server.ServerExceptionMapper;

import jakarta.persistence.PersistenceException;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;

public class ErrorHandler {

    private static final Logger LOG = Logger.getLogger(ErrorHandler.class);

    @ServerExceptionMapper
    public Response persistencia(PersistenceException e) {
        return errorBD(e);
    }

    @ServerExceptionMapper
    public Response hibernate(HibernateException e) {
        return errorBD(e);
    }

    private Response errorBD(Exception e) {
        LOG.error("Error de persistencia", e);
        return Response.status(Response.Status.INTERNAL_SERVER_ERROR)
                .type(MediaType.APPLICATION_JSON)
                .entity(Map.of("error", "Error al acceder a la base de datos"))
                .build();
    }
}
